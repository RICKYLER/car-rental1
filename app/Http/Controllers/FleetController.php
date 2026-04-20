<?php

namespace App\Http\Controllers;

use App\Models\ChargingStation;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class FleetController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', 'in:available,reserved,charging,maintenance'],
            'connector' => ['nullable', 'string', 'max:50'],
            'zone' => ['nullable', 'string', 'max:50'],
            'min_soc' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $vehicles = Vehicle::query()
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['connector'] ?? null, fn ($query, $connector) => $query->where('connector_type', $connector))
            ->when($filters['zone'] ?? null, fn ($query, $zone) => $query->where('location_zone', $zone))
            ->when(isset($filters['min_soc']), fn ($query) => $query->where('battery_soc', '>=', (int) $filters['min_soc']))
            ->orderByRaw("case status when 'available' then 0 when 'charging' then 1 when 'reserved' then 2 else 3 end")
            ->orderByDesc('battery_soc')
            ->get();

        $connectors = Vehicle::query()->select('connector_type')->distinct()->orderBy('connector_type')->pluck('connector_type');
        $zones = Vehicle::query()->select('location_zone')->distinct()->orderBy('location_zone')->pluck('location_zone');

        return view('fleet.index', compact('vehicles', 'filters', 'connectors', 'zones'));
    }

    public function show(Vehicle $vehicle): View
    {
        $vehicle->load([
            'bookings' => fn ($query) => $query->with('user')->latest('start_at'),
            'chargingSessions' => fn ($query) => $query->with('chargingStation')->latest('expected_completion_at'),
        ]);

        $compatibleStations = ChargingStation::query()
            ->where('connector_type', $vehicle->connector_type)
            ->orderByDesc('available_ports')
            ->orderBy('distance_from_hub_km')
            ->take(4)
            ->get();

        return view('fleet.show', compact('vehicle', 'compatibleStations'));
    }
}
