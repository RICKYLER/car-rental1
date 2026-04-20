<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ChargingStation;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $availableVehicles = Vehicle::where('status', 'available')->count();
        $averageBattery = (int) round((float) Vehicle::avg('battery_soc'));
        $activeBookings = Booking::whereIn('status', ['confirmed', 'active'])->count();
        $co2AvoidedKg = (int) round((float) Booking::whereIn('status', ['confirmed', 'active', 'completed'])->sum('estimated_distance_km') * 0.18);

        $metrics = [
            [
                'label' => 'Fleet ready now',
                'value' => $availableVehicles,
                'caption' => 'EVs with enough charge for immediate dispatch',
            ],
            [
                'label' => 'Average battery',
                'value' => $averageBattery.'%',
                'caption' => 'Live state of charge across the entire mock fleet',
            ],
            [
                'label' => 'Active journeys',
                'value' => $activeBookings,
                'caption' => 'Confirmed and in-progress rentals tracked in real time',
            ],
            [
                'label' => 'CO2 avoided',
                'value' => $co2AvoidedKg.' kg',
                'caption' => 'Estimated emissions displaced by the current bookings',
            ],
        ];

        $operations = [
            [
                'title' => 'Live telematics',
                'copy' => 'Track battery state of charge, GPS position, and current vehicle readiness from a single operations layer.',
            ],
            [
                'title' => 'Smart charging',
                'copy' => 'Plan post-trip charging automatically when projected return battery levels fall below the safe threshold.',
            ],
            [
                'title' => 'Range-aware booking',
                'copy' => 'Reject unsafe trips early, price energy use transparently, and suggest compatible charging stations by connector type.',
            ],
        ];

        $featuredVehicles = Vehicle::query()
            ->orderByRaw("case status when 'available' then 0 when 'charging' then 1 when 'reserved' then 2 else 3 end")
            ->orderByDesc('battery_soc')
            ->take(4)
            ->get();

        $stationHighlights = ChargingStation::query()
            ->orderByDesc('available_ports')
            ->orderBy('distance_from_hub_km')
            ->take(3)
            ->get();

        $recentBookings = Booking::query()
            ->with(['user', 'vehicle'])
            ->latest('start_at')
            ->take(3)
            ->get();

        return view('home', compact(
            'metrics',
            'operations',
            'featuredVehicles',
            'stationHighlights',
            'recentBookings',
        ));
    }
}
