@extends('layouts.app')

@section('title', 'ECROS Admin')

@section('content')
    <section class="section-heading section-heading--intro">
        <div>
            <span class="eyebrow">Fleet command center</span>
            <h1>Admin telemetry, charging, and fleet readiness in a cleaner control surface.</h1>
            <p class="lead">
                The customer-facing polish now carries into the operations layer while preserving dark-card contrast for critical monitoring.
            </p>
        </div>
    </section>

    <section class="metric-grid">
        <div class="metric-card">
            <span>Total revenue</span>
            <strong>PHP {{ number_format((float) $stats['revenue'], 2) }}</strong>
        </div>
        <div class="metric-card">
            <span>Active rentals</span>
            <strong>{{ $stats['activeRentals'] }}</strong>
        </div>
        <div class="metric-card">
            <span>Average battery health</span>
            <strong>{{ $stats['avgHealth'] }}%</strong>
        </div>
        <div class="metric-card">
            <span>Charging jobs queued</span>
            <strong>{{ $stats['gridReady'] }}</strong>
        </div>
    </section>

    <section class="section section--split">
        <div class="panel panel--dark">
            <div class="section-heading">
                <div>
                    <span class="eyebrow eyebrow--dark">Operational alerts</span>
                    <h2>Issues needing attention</h2>
                </div>
            </div>
            <div class="stack-list">
                @forelse ($alerts as $alert)
                    <article class="alert-card">
                        <span class="alert-card__level">{{ $alert['level'] }}</span>
                        <h3>{{ $alert['title'] }}</h3>
                        <p>{{ $alert['copy'] }}</p>
                    </article>
                @empty
                    <article class="list-card">
                        <div>
                            <h3>No active alerts</h3>
                            <p>The fleet is within the current mock thresholds.</p>
                        </div>
                    </article>
                @endforelse
            </div>
        </div>

        <div class="panel">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Charging scheduler</span>
                    <h2>Queue and live charging sessions</h2>
                </div>
            </div>
            <div class="stack-list">
                @foreach ($chargingQueue as $session)
                    <article class="list-card">
                        <div>
                            <h3>{{ $session->vehicle->name }}</h3>
                            <p>{{ $session->chargingStation->name }}</p>
                        </div>
                        <div class="list-card__meta">
                            <strong>{{ ucfirst(str_replace('_', ' ', $session->status)) }}</strong>
                            <span>Target {{ $session->target_soc }}%</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="panel">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Fleet monitor</span>
                <h2>Vehicle health and readiness</h2>
            </div>
        </div>
        <div class="stack-list">
            @foreach ($vehicles as $vehicle)
                <article class="list-card">
                    <div>
                        <h3>{{ $vehicle->name }}</h3>
                        <p>{{ ucfirst($vehicle->status) }} &middot; {{ $vehicle->location_zone }}</p>
                    </div>
                    <div class="list-card__meta">
                        <strong>{{ $vehicle->battery_soc }}% battery / {{ $vehicle->battery_health }}% health</strong>
                        <span>
                            @if ($vehicle->chargingSessions->isNotEmpty())
                                {{ ucfirst(str_replace('_', ' ', $vehicle->chargingSessions->first()->status)) }} at {{ $vehicle->chargingSessions->first()->chargingStation->name }}
                            @elseif ($vehicle->bookings->isNotEmpty())
                                Booking {{ $vehicle->bookings->first()->reference }}
                            @else
                                Ready for dispatch
                            @endif
                        </span>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="panel">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Recent reservations</span>
                <h2>Latest booking activity</h2>
            </div>
        </div>
        <div class="stack-list">
            @foreach ($recentBookings as $booking)
                <article class="list-card">
                    <div>
                        <h3>{{ $booking->reference }}</h3>
                        <p>{{ $booking->user->name }} &middot; {{ $booking->vehicle->name }}</p>
                    </div>
                    <div class="list-card__meta">
                        <strong>{{ ucfirst($booking->status) }}</strong>
                        <span>PHP {{ number_format((float) $booking->total_cost, 2) }}</span>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endsection
