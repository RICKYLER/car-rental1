<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ChargingSession;
use App\Models\ChargingStation;
use App\Models\RemoteCommand;
use App\Models\SecurityEvent;
use App\Models\SystemSetting;
use App\Models\Vehicle;
use App\Services\ChargingIntelligenceService;
use App\Services\OperationsSimulationService;
use App\Services\TelematicsService;
use Illuminate\Contracts\View\View;

class AdminDashboardController extends Controller
{
    public function __construct(
        private readonly ChargingIntelligenceService $chargingIntelligence,
        private readonly OperationsSimulationService $simulation,
        private readonly TelematicsService $telematics,
    ) {
    }

    public function index(): View
    {
        $vehicles = Vehicle::query()
            ->with([
                'bookings' => fn ($query) => $query->latest('start_at'),
                'chargingSessions' => fn ($query) => $query->with('chargingStation')->latest('expected_completion_at'),
                'latestTelemetry',
                'telematics' => fn ($query) => $query->latest('observed_at'),
            ])
            ->orderByRaw("case status when 'available' then 0 when 'charging' then 1 when 'reserved' then 2 else 3 end")
            ->orderBy('battery_soc')
            ->get();
        $this->telematics->attachSummaries($vehicles);

        $chargingQueue = ChargingSession::query()
            ->with(['vehicle', 'chargingStation'])
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->orderBy('expected_completion_at')
            ->get();
        $this->chargingIntelligence->attachSignals($chargingQueue->pluck('chargingStation')->filter()->values());

        $recentBookings = Booking::query()
            ->with(['user', 'vehicle'])
            ->latest('start_at')
            ->take(6)
            ->get();

        $remoteCommands = RemoteCommand::query()
            ->with(['vehicle', 'requester'])
            ->latest()
            ->take(5)
            ->get();

        $recentSecurityEvents = SecurityEvent::query()
            ->latest('detected_at')
            ->take(5)
            ->get();

        $staleVehicles = $vehicles->filter(
            fn (Vehicle $vehicle) => ($vehicle->telemetry_summary['freshness'] ?? null) === 'stale'
        );
        $failedLoginAttempts = SecurityEvent::query()
            ->where('event_type', 'failed_login')
            ->where('detected_at', '>=', now()->subDay())
            ->count();
        $failedRemoteCommands = RemoteCommand::query()
            ->where('result_status', 'rejected')
            ->where('created_at', '>=', now()->subDay())
            ->count();
        $avgWaitTime = (int) round(
            $chargingQueue->count() / max(1, (int) ChargingStation::query()->sum('live_availability')) * 18
        );

        $stats = [
            'revenue' => (float) Booking::sum('total_cost'),
            'activeRentals' => Booking::whereIn('status', ['confirmed', 'active'])->count(),
            'avgHealth' => (int) round((float) Vehicle::avg('battery_health')),
            'gridReady' => $chargingQueue->where('status', 'scheduled')->count(),
        ];

        $analytics = [
            'downtime_rate' => (int) round(($vehicles->whereIn('status', ['charging', 'maintenance'])->count() / max(1, $vehicles->count())) * 100),
            'failed_booking_rate' => (int) round((Booking::query()->where('projected_return_soc', '<', 20)->count() / max(1, Booking::query()->count())) * 100),
            'charger_wait_time' => $avgWaitTime,
            'stale_data_rate' => (int) round(($staleVehicles->count() / max(1, $vehicles->count())) * 100),
            'range_risk_incidents' => Booking::query()->where('projected_return_soc', '<', 30)->count(),
        ];

        $alerts = collect();

        Vehicle::query()
            ->where('battery_soc', '<', 25)
            ->orderBy('battery_soc')
            ->get()
            ->each(function (Vehicle $vehicle) use ($alerts): void {
                $alerts->push([
                    'level' => 'Critical',
                    'title' => "{$vehicle->name} battery below dispatch threshold",
                    'copy' => "Only {$vehicle->battery_soc}% charge remains in {$vehicle->location_zone}.",
                ]);
            });

        Vehicle::query()
            ->whereNotNull('next_service_due_at')
            ->where('next_service_due_at', '<=', now()->addDays(14))
            ->orderBy('next_service_due_at')
            ->get()
            ->each(function (Vehicle $vehicle) use ($alerts): void {
                $alerts->push([
                    'level' => 'Watch',
                    'title' => "{$vehicle->name} needs maintenance soon",
                    'copy' => 'Next service window closes on '.$vehicle->next_service_due_at?->format('M d, Y').'.',
                ]);
            });

        ChargingStation::query()
            ->where('available_ports', 0)
            ->orderBy('name')
            ->get()
            ->each(function (ChargingStation $station) use ($alerts): void {
                $alerts->push([
                    'level' => 'Queue',
                    'title' => "{$station->name} is fully occupied",
                    'copy' => 'Charging demand in '.$station->zone.' is higher than current port availability.',
                ]);
            });

        $staleVehicles->each(function (Vehicle $vehicle) use ($alerts): void {
            $alerts->push([
                'level' => 'Stale',
                'title' => "{$vehicle->name} has stale telemetry",
                'copy' => 'Last trusted snapshot is '.$vehicle->telemetry_summary['observed_at']?->diffForHumans().'. Routing confidence is now estimated.',
            ]);
        });

        if ($failedLoginAttempts >= 3) {
            $alerts->push([
                'level' => 'Security',
                'title' => 'Repeated login failures detected',
                'copy' => "{$failedLoginAttempts} rejected sign-ins were logged in the last 24 hours.",
            ]);
        }

        if ($failedRemoteCommands > 0) {
            $alerts->push([
                'level' => 'Security',
                'title' => 'Remote command rejections were recorded',
                'copy' => "{$failedRemoteCommands} sensitive commands failed step-up verification in the last 24 hours.",
            ]);
        }

        $impossibleJumpAlerts = $this->telematics->impossibleJumpAlerts($vehicles);
        $impossibleJumpAlerts->each(fn (array $alert) => $alerts->push($alert));

        return view('admin.dashboard', [
            'vehicles' => $vehicles,
            'chargingQueue' => $chargingQueue,
            'recentBookings' => $recentBookings,
            'remoteCommands' => $remoteCommands,
            'recentSecurityEvents' => $recentSecurityEvents,
            'stats' => $stats,
            'analytics' => $analytics,
            'alerts' => $alerts->take(6),
            'relocationRecommendations' => $this->chargingIntelligence->relocationRecommendations($vehicles),
            'simulationScenarios' => $this->simulation->scenarios(),
            'commandableVehicles' => $vehicles,
            'v2gEnabled' => (bool) SystemSetting::getValue('v2g_enabled', false),
        ]);
    }
}
