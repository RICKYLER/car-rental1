@extends('layouts.app')

@section('title', $vehicle->name)

@section('content')
    @php
        $latestBooking = $vehicle->bookings->first();
        $latestCharging = $vehicle->chargingSessions->first();
    @endphp

    <section class="detail-hero">
        <div class="panel panel--accent detail-hero__summary" style="--vehicle-accent: {{ $vehicle->accent_color }}">
            <span class="eyebrow">Vehicle profile</span>
            <h1>{{ $vehicle->name }}</h1>
            <p class="lead">{{ $vehicle->description }}</p>

            <div class="metric-grid">
                <div class="metric-card">
                    <span>Battery</span>
                    <strong>{{ $vehicle->battery_soc }}%</strong>
                </div>
                <div class="metric-card">
                    <span>Estimated range</span>
                    <strong>{{ $vehicle->estimated_range_km }} km</strong>
                </div>
                <div class="metric-card">
                    <span>Battery health</span>
                    <strong>{{ $vehicle->battery_health }}%</strong>
                </div>
                <div class="metric-card">
                    <span>Connector</span>
                    <strong>{{ $vehicle->connector_type }}</strong>
                </div>
            </div>

            <div class="hero__actions">
                @if ($vehicle->status === 'available')
                    <a class="btn btn-primary" href="{{ route('bookings.create', ['vehicle' => $vehicle->id]) }}">Book this vehicle</a>
                @endif
                <a class="btn btn-secondary" href="{{ route('fleet.index') }}">Back to fleet</a>
            </div>
        </div>

        <div class="panel panel--dark">
            <span class="eyebrow eyebrow--dark">Pricing and readiness</span>
            <div class="stack-list">
                <article class="list-card">
                    <div>
                        <h3>Rental pricing</h3>
                        <p>Day rate plus transparent energy-aware trip costs</p>
                    </div>
                    <div class="list-card__meta">
                        <strong>PHP {{ number_format((float) $vehicle->daily_rate, 0) }}/day</strong>
                        <span>PHP {{ number_format((float) $vehicle->per_km_rate, 2) }}/km</span>
                    </div>
                </article>
                <article class="list-card">
                    <div>
                        <h3>Current location</h3>
                        <p>Last seen {{ optional($vehicle->last_seen_at)->diffForHumans() ?? 'N/A' }}</p>
                    </div>
                    <div class="list-card__meta">
                        <strong>{{ $vehicle->location_zone }}</strong>
                        <span>{{ $vehicle->plate_number }}</span>
                    </div>
                </article>
                <article class="list-card">
                    <div>
                        <h3>Maintenance window</h3>
                        <p>Next scheduled service target</p>
                    </div>
                    <div class="list-card__meta">
                        <strong>{{ optional($vehicle->next_service_due_at)->format('M d, Y') ?? 'TBD' }}</strong>
                        <span>Odometer {{ number_format($vehicle->odometer_km) }} km</span>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="section section--split">
        <div class="panel">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Charging options</span>
                    <h2>Compatible stations nearby</h2>
                </div>
            </div>
            <div class="stack-list">
                @foreach ($compatibleStations as $station)
                    <article class="list-card">
                        <div>
                            <h3>{{ $station->name }}</h3>
                            <p>{{ $station->location }}</p>
                        </div>
                        <div class="list-card__meta">
                            <strong>{{ $station->available_ports }}/{{ $station->total_ports }} ports</strong>
                            <span>{{ number_format((float) $station->distance_from_hub_km, 1) }} km away</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

        <div class="panel">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Latest activity</span>
                    <h2>Operational context</h2>
                </div>
            </div>
            <div class="stack-list">
                @if ($latestBooking)
                    <article class="list-card">
                        <div>
                            <h3>Latest booking</h3>
                            <p>{{ $latestBooking->reference }} &middot; {{ $latestBooking->user->name }}</p>
                        </div>
                        <div class="list-card__meta">
                            <strong>{{ ucfirst($latestBooking->status) }}</strong>
                            <span>{{ $latestBooking->start_at->format('M d, h:i A') }}</span>
                        </div>
                    </article>
                @endif

                @if ($latestCharging)
                    <article class="list-card">
                        <div>
                            <h3>Charging session</h3>
                            <p>{{ $latestCharging->chargingStation->name }}</p>
                        </div>
                        <div class="list-card__meta">
                            <strong>{{ ucfirst(str_replace('_', ' ', $latestCharging->status)) }}</strong>
                            <span>Target {{ $latestCharging->target_soc }}%</span>
                        </div>
                    </article>
                @endif

                @if (! $latestBooking && ! $latestCharging)
                    <article class="list-card">
                        <div>
                            <h3>No recent operations logged</h3>
                            <p>This vehicle is idle in the current mock dataset.</p>
                        </div>
                    </article>
                @endif
            </div>
        </div>
    </section>
@endsection
