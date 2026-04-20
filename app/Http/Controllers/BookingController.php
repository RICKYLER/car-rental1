<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ChargingSession;
use App\Models\ChargingStation;
use App\Models\Vehicle;
use App\Services\BookingEstimator;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function __construct(private readonly BookingEstimator $estimator)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $bookings = Booking::query()
            ->with(['user', 'vehicle'])
            ->when($user->isCustomer(), fn ($query) => $query->whereBelongsTo($user))
            ->latest('start_at')
            ->get();

        $summary = [
            'total' => $bookings->count(),
            'confirmed' => $bookings->where('status', 'confirmed')->count(),
            'active' => $bookings->where('status', 'active')->count(),
            'value' => $bookings->sum('total_cost'),
        ];

        return view('bookings.index', [
            'bookings' => $bookings,
            'summary' => $summary,
            'viewer' => $user,
        ]);
    }

    public function create(Request $request): View
    {
        $vehicles = Vehicle::query()
            ->where('status', 'available')
            ->orderByDesc('battery_soc')
            ->get();

        $selectedVehicle = $request->filled('vehicle')
            ? $vehicles->firstWhere('id', (int) $request->integer('vehicle'))
            : $vehicles->first();

        return view('bookings.create', [
            'vehicles' => $vehicles,
            'selectedVehicle' => $selectedVehicle,
            'customer' => $request->user(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'pickup_location' => ['required', 'string', 'max:255'],
            'dropoff_location' => ['required', 'string', 'max:255'],
            'start_at' => ['required', 'date', 'after:now'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'estimated_distance_km' => ['required', 'integer', 'min:10', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $vehicle = Vehicle::query()->where('status', 'available')->findOrFail($data['vehicle_id']);

        $startAt = Carbon::parse($data['start_at']);
        $endAt = Carbon::parse($data['end_at']);
        $quote = $this->estimator->estimate($vehicle, $startAt, $endAt, (int) $data['estimated_distance_km']);

        if ((int) $data['estimated_distance_km'] > $quote['safe_range_km']) {
            return back()
                ->withInput()
                ->withErrors([
                    'estimated_distance_km' => 'This trip exceeds the safe EV range for the selected vehicle. Choose a shorter route or a different car.',
                ]);
        }

        $booking = DB::transaction(function () use ($data, $vehicle, $startAt, $endAt, $quote, $user) {
            $user->forceFill([
                'preferred_zone' => $data['pickup_location'],
            ])->save();

            $booking = Booking::create([
                'user_id' => $user->id,
                'vehicle_id' => $vehicle->id,
                'status' => 'confirmed',
                'pickup_location' => $data['pickup_location'],
                'dropoff_location' => $data['dropoff_location'],
                'start_at' => $startAt,
                'end_at' => $endAt,
                'estimated_distance_km' => $data['estimated_distance_km'],
                'estimated_energy_kwh' => $quote['estimated_energy_kwh'],
                'projected_return_soc' => $quote['projected_return_soc'],
                'base_cost' => $quote['base_cost'],
                'distance_cost' => $quote['distance_cost'],
                'energy_cost' => $quote['energy_cost'],
                'battery_wear_cost' => $quote['battery_wear_cost'],
                'total_cost' => $quote['total_cost'],
                'license_verified' => $user->license_verified,
                'notes' => $data['notes'] ?? null,
            ]);

            $vehicle->update(['status' => 'reserved']);

            if ($quote['projected_return_soc'] < 30) {
                $station = ChargingStation::query()
                    ->where('connector_type', $vehicle->connector_type)
                    ->where('available_ports', '>', 0)
                    ->orderBy('price_per_kwh')
                    ->orderBy('distance_from_hub_km')
                    ->first();

                if ($station) {
                    $energyNeeded = round((float) $vehicle->battery_capacity_kwh * ((90 - $quote['projected_return_soc']) / 100), 1);

                    ChargingSession::create([
                        'vehicle_id' => $vehicle->id,
                        'charging_station_id' => $station->id,
                        'status' => 'scheduled',
                        'expected_completion_at' => $endAt->copy()->addHours(3),
                        'current_soc' => $quote['projected_return_soc'],
                        'target_soc' => 90,
                        'energy_kwh' => $energyNeeded,
                        'estimated_cost' => round((float) $station->price_per_kwh * $energyNeeded, 2),
                    ]);
                }
            }

            return $booking;
        });

        return redirect()
            ->route('bookings.index')
            ->with('status', "Booking {$booking->reference} confirmed. The mock fleet schedule and billing figures were updated.");
    }
}
