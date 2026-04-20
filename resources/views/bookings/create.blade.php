@extends('layouts.app')

@section('title', 'Create Booking')

@section('content')
    <section class="section-heading section-heading--intro">
        <div>
            <span class="eyebrow">Booking engine</span>
            <h1>Create a protected customer reservation.</h1>
            <p class="lead">
                Your account details are already on file, so this flow focuses on the trip itself, vehicle range, and pricing clarity.
            </p>
        </div>
        <a class="btn btn-secondary" href="{{ route('bookings.index') }}">View bookings</a>
    </section>

    <section class="section section--split booking-layout">
        <div class="panel panel--accent">
            <span class="eyebrow">Reservation form</span>

            @if ($vehicles->isEmpty())
                <div class="empty-state">
                    <h2>No vehicles are currently dispatchable.</h2>
                    <p>Seed the database again or free up an EV from the admin side.</p>
                </div>
            @else
                <form method="POST" action="{{ route('bookings.store') }}" class="form-grid">
                    @csrf

                    <label>
                        <span>Vehicle</span>
                        <select name="vehicle_id" required>
                            @foreach ($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" @selected((int) old('vehicle_id', optional($selectedVehicle)->id) === $vehicle->id)>
                                    {{ $vehicle->name }} - {{ $vehicle->battery_soc }}% - {{ $vehicle->estimated_range_km }} km
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label>
                        <span>Pickup location</span>
                        <input type="text" name="pickup_location" value="{{ old('pickup_location', $customer->preferred_zone ?? optional($selectedVehicle)->location_zone) }}" required>
                    </label>

                    <label>
                        <span>Drop-off location</span>
                        <input type="text" name="dropoff_location" value="{{ old('dropoff_location') }}" required>
                    </label>

                    <label>
                        <span>Start time</span>
                        <input type="datetime-local" name="start_at" value="{{ old('start_at', now()->addDay()->format('Y-m-d\TH:i')) }}" required>
                    </label>

                    <label>
                        <span>End time</span>
                        <input type="datetime-local" name="end_at" value="{{ old('end_at', now()->addDays(2)->format('Y-m-d\TH:i')) }}" required>
                    </label>

                    <label class="form-grid__full">
                        <span>Estimated trip distance (km)</span>
                        <input type="number" min="10" max="1000" name="estimated_distance_km" value="{{ old('estimated_distance_km', 80) }}" required>
                    </label>

                    <label class="form-grid__full">
                        <span>Trip notes</span>
                        <textarea name="notes" rows="4" placeholder="Optional trip purpose, charging request, or operational notes">{{ old('notes') }}</textarea>
                    </label>

                    <div class="form-grid__full">
                        <button class="btn btn-primary" type="submit">Confirm booking</button>
                    </div>
                </form>
            @endif
        </div>

        <div class="sidebar-stack">
            <div class="panel panel--dark">
                <span class="eyebrow eyebrow--dark">Selected vehicle</span>

                @if ($selectedVehicle)
                    <article class="list-card">
                        <div>
                            <h3>{{ $selectedVehicle->name }}</h3>
                            <p>{{ $selectedVehicle->location_zone }} &middot; {{ $selectedVehicle->connector_type }}</p>
                        </div>
                        <div class="list-card__meta">
                            <strong>{{ $selectedVehicle->battery_soc }}%</strong>
                            <span>{{ $selectedVehicle->telemetry_summary['range_km'] }} km {{ strtolower($selectedVehicle->telemetry_summary['freshness_label']) }} range</span>
                        </div>
                    </article>
                    <div class="telemetry-pills">
                        <span class="signal-pill signal-pill--{{ $selectedVehicle->telemetry_summary['confidence_tone'] }}">
                            {{ $selectedVehicle->telemetry_summary['freshness_label'] }}
                        </span>
                        <span class="signal-pill">{{ $selectedVehicle->telemetry_summary['signal_label'] }}</span>
                    </div>
                    <div class="metric-grid metric-grid--compact">
                        <div class="metric-card metric-card--dark">
                            <span>Daily rate</span>
                            <strong>PHP {{ number_format((float) $selectedVehicle->daily_rate, 0) }}</strong>
                        </div>
                        <div class="metric-card metric-card--dark">
                            <span>Distance</span>
                            <strong>PHP {{ number_format((float) $selectedVehicle->per_km_rate, 2) }}/km</strong>
                        </div>
                        <div class="metric-card metric-card--dark">
                            <span>Energy</span>
                            <strong>PHP {{ number_format((float) $selectedVehicle->energy_rate, 2) }}/kWh</strong>
                        </div>
                        <div class="metric-card metric-card--dark">
                            <span>Safe range</span>
                            <strong>{{ $previewQuote['safe_range_km'] ?? max($selectedVehicle->estimated_range_km - 25, 0) }} km</strong>
                        </div>
                    </div>
                @endif
            </div>

            @if ($previewQuote && $selectedVehicle)
                <div class="panel">
                    <span class="eyebrow">Trip confidence</span>
                    <div class="metric-grid metric-grid--compact">
                        <div class="metric-card">
                            <span>Projected return</span>
                            <strong>{{ $previewQuote['projected_return_soc'] }}%</strong>
                        </div>
                        <div class="metric-card">
                            <span>Safety floor</span>
                            <strong>{{ $previewQuote['minimum_return_soc'] }}%</strong>
                        </div>
                        <div class="metric-card">
                            <span>Return credit</span>
                            <strong>PHP {{ number_format((float) $previewQuote['return_incentive_credit'], 2) }}</strong>
                        </div>
                        <div class="metric-card">
                            <span>Deep-discharge fee</span>
                            <strong>PHP {{ number_format((float) $previewQuote['deep_discharge_fee'], 2) }}</strong>
                        </div>
                    </div>

                    <div class="stack-list">
                        <article class="list-card">
                            <div>
                                <h3>
                                    @if ($previewQuote['requires_intervention'])
                                        Booking intervention required
                                    @elseif ($previewQuote['needs_charging_fallback'])
                                        Charge stop recommended
                                    @else
                                        Trip confidence looks good
                                    @endif
                                </h3>
                                <p>
                                    @if ($previewQuote['requires_intervention'])
                                        This route returns below the minimum safe SoC. Reduce distance or rely on a compatible charger.
                                    @elseif ($previewQuote['needs_charging_fallback'])
                                        ECROS will reserve a charging fallback because the projected return SoC is below 30%.
                                    @else
                                        The selected route stays above the warning threshold with the current battery snapshot.
                                    @endif
                                </p>
                            </div>
                            <div class="list-card__meta">
                                <strong>PHP {{ number_format((float) $previewQuote['total_cost'], 2) }}</strong>
                                <span>Total projected cost</span>
                            </div>
                        </article>
                    </div>
                </div>
            @endif

            @if ($recommendedStations->isNotEmpty())
                <div class="panel">
                    <span class="eyebrow">Suggested chargers</span>
                    <div class="stack-list">
                        @foreach ($recommendedStations as $station)
                            <article class="list-card">
                                <div>
                                    <h3>{{ $station->name }}</h3>
                                    <p>{{ $station->confidence_summary }}</p>
                                </div>
                                <div class="list-card__meta">
                                    <strong>{{ $station->risk_label }}</strong>
                                    <span>{{ $station->live_ports }}/{{ $station->total_ports }} ports</span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="panel">
                <span class="eyebrow">Your account</span>
                <div class="stack-list">
                    <article class="list-card">
                        <div>
                            <h3>{{ $customer->name }}</h3>
                            <p>{{ $customer->email }}</p>
                        </div>
                        <div class="list-card__meta">
                            <strong>{{ $customer->phone }}</strong>
                            <span>{{ $customer->preferred_zone ?? 'No saved zone' }}</span>
                        </div>
                    </article>
                    <article class="list-card">
                        <div>
                            <h3>EV trip reminders</h3>
                            <p>Return above 40% SoC to keep your trip smoother and unlock the mock return credit.</p>
                        </div>
                        <div class="list-card__meta">
                            <strong>{{ $customer->license_verified ? 'Verified driver' : 'Pending' }}</strong>
                            <span>Avoid deep discharge below 15%</span>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
@endsection
