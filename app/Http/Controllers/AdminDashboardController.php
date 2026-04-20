<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ChargingSession;
use App\Models\ChargingStation;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $vehicles = Vehicle::query()
            ->with([
                'bookings' => fn ($query) => $query->latest('start_at'),
                'chargingSessions' => fn ($query) => $query->with('chargingStation')->latest('expected_completion_at'),
            ])
            ->orderByRaw("case status when 'available' then 0 when 'charging' then 1 when 'reserved' then 2 else 3 end")
            ->orderBy('battery_soc')
            ->get();

        $chargingQueue = ChargingSession::query()
            ->with(['vehicle', 'chargingStation'])
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->orderBy('expected_completion_at')
            ->get();

        $recentBookings = Booking::query()
            ->with(['user', 'vehicle'])
            ->latest('start_at')
            ->take(6)
            ->get();

        $stats = [
            'revenue' => (float) Booking::sum('total_cost'),
            'activeRentals' => Booking::whereIn('status', ['confirmed', 'active'])->count(),
            'avgHealth' => (int) round((float) Vehicle::avg('battery_health')),
            'gridReady' => $chargingQueue->where('status', 'scheduled')->count(),
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

        return view('admin.dashboard', [
            'vehicles' => $vehicles,
            'chargingQueue' => $chargingQueue,
            'recentBookings' => $recentBookings,
            'stats' => $stats,
            'alerts' => $alerts->take(6),
        ]);
    }
}
