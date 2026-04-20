<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ChargingStation;
use App\Models\Vehicle;
use App\Services\ChargingIntelligenceService;
use App\Services\TelematicsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomerDashboardController extends Controller
{
    public function __construct(
        private readonly ChargingIntelligenceService $chargingIntelligence,
        private readonly TelematicsService $telematics,
    ) {
    }

    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $bookings = Booking::query()
            ->with('vehicle')
            ->whereBelongsTo($user)
            ->latest('start_at')
            ->get();

        $upcomingTrips = $bookings->whereIn('status', ['confirmed', 'active'])->take(3);

        $summary = [
            'bookings' => $bookings->count(),
            'upcoming' => $bookings->whereIn('status', ['confirmed', 'active'])->count(),
            'completed' => $bookings->where('status', 'completed')->count(),
            'spent' => $bookings->sum('total_cost'),
        ];

        $recommendedVehicles = Vehicle::query()
            ->with('latestTelemetry')
            ->where('status', 'available')
            ->when($user->preferred_zone, fn ($query, $zone) => $query->orderByRaw('location_zone = ? desc', [$zone]))
            ->orderByDesc('battery_soc')
            ->take(3)
            ->get();
        $this->telematics->attachSummaries($recommendedVehicles);

        $stationHighlights = $this->chargingIntelligence->attachSignals(
            ChargingStation::query()->get()
        )
            ->sortByDesc('ranking_score')
            ->take(2)
            ->values();

        $upcomingTrips->each(function (Booking $booking): void {
            $booking->setAttribute('trip_confidence_label', match (true) {
                $booking->projected_return_soc < 20 => 'Intervention needed',
                $booking->projected_return_soc < 30 => 'Charge stop recommended',
                default => 'Trip confidence good',
            });
        });

        return view('customer.dashboard', [
            'customer' => $user,
            'summary' => $summary,
            'upcomingTrips' => $upcomingTrips,
            'recommendedVehicles' => $recommendedVehicles,
            'stationHighlights' => $stationHighlights,
        ]);
    }
}
