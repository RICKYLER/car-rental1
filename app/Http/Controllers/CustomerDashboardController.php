<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ChargingStation;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomerDashboardController extends Controller
{
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
            ->where('status', 'available')
            ->when($user->preferred_zone, fn ($query, $zone) => $query->orderByRaw('location_zone = ? desc', [$zone]))
            ->orderByDesc('battery_soc')
            ->take(3)
            ->get();

        $stationHighlights = ChargingStation::query()
            ->orderByDesc('available_ports')
            ->orderBy('distance_from_hub_km')
            ->take(2)
            ->get();

        return view('customer.dashboard', [
            'customer' => $user,
            'summary' => $summary,
            'upcomingTrips' => $upcomingTrips,
            'recommendedVehicles' => $recommendedVehicles,
            'stationHighlights' => $stationHighlights,
        ]);
    }
}
