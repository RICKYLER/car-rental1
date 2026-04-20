@extends('layouts.app')

@section('title', 'Customer Dashboard | ECROS')

@section('content')
    <section class="hero hero--home">
        <div class="hero__copy panel panel--accent">
            <span class="eyebrow">Customer dashboard</span>
            <h1>Welcome back, {{ $customer->name }}.</h1>
            <p class="lead">
                Review upcoming trips, monitor your account status, and reserve your next electric vehicle with less friction.
            </p>
            <div class="hero__actions">
                <a class="btn btn-primary" href="{{ route('bookings.create') }}">Create new booking</a>
                <a class="btn btn-secondary" href="{{ route('fleet.index') }}">Browse fleet</a>
            </div>
        </div>

        <div class="hero__stats">
            <div class="panel quick-panel">
                <div class="quick-panel__header">
                    <span class="eyebrow">Account summary</span>
                    <strong>Ready to book</strong>
                </div>
                <div class="metric-grid metric-grid--compact">
                    <div class="metric-card">
                        <span>Total trips</span>
                        <strong>{{ $summary['bookings'] }}</strong>
                    </div>
                    <div class="metric-card">
                        <span>Upcoming</span>
                        <strong>{{ $summary['upcoming'] }}</strong>
                    </div>
                    <div class="metric-card">
                        <span>Completed</span>
                        <strong>{{ $summary['completed'] }}</strong>
                    </div>
                    <div class="metric-card">
                        <span>Total spend</span>
                        <strong>PHP {{ number_format((float) $summary['spent'], 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section section--split">
        <div class="panel">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Upcoming trips</span>
                    <h2>Your next bookings</h2>
                </div>
                <a class="btn btn-secondary" href="{{ route('bookings.index') }}">Open all trips</a>
            </div>

            <div class="stack-list">
                @forelse ($upcomingTrips as $booking)
                    <article class="list-card">
                        <div>
                            <h3>{{ $booking->reference }}</h3>
                            <p>{{ $booking->vehicle->name }} &middot; {{ $booking->pickup_location }} to {{ $booking->dropoff_location }}</p>
                        </div>
                        <div class="list-card__meta">
                            <strong>{{ $booking->trip_confidence_label }}</strong>
                            <span>{{ $booking->start_at->format('M d, h:i A') }} &middot; {{ $booking->projected_return_soc }}% projected return</span>
                        </div>
                    </article>
                @empty
                    <div class="empty-state empty-state--compact">
                        <h3>No upcoming trips yet</h3>
                        <p>Create your first booking to start tracking EV trips from this dashboard.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="panel panel--dark">
            <div class="section-heading">
                <div>
                    <span class="eyebrow eyebrow--dark">Profile snapshot</span>
                    <h2>Your account details</h2>
                </div>
            </div>

            <div class="profile-list">
                <article class="list-card">
                    <div>
                        <h3>Email</h3>
                        <p>Primary login and notification address</p>
                    </div>
                    <div class="list-card__meta">
                        <strong>{{ $customer->email }}</strong>
                    </div>
                </article>
                <article class="list-card">
                    <div>
                        <h3>Phone</h3>
                        <p>Customer contact number</p>
                    </div>
                    <div class="list-card__meta">
                        <strong>{{ $customer->phone }}</strong>
                    </div>
                </article>
                <article class="list-card">
                    <div>
                        <h3>Preferred zone</h3>
                        <p>Last saved pickup area</p>
                    </div>
                    <div class="list-card__meta">
                        <strong>{{ $customer->preferred_zone ?? 'Not set' }}</strong>
                    </div>
                </article>
                <article class="list-card">
                    <div>
                        <h3>License verification</h3>
                        <p>Mock identity status used in booking records</p>
                    </div>
                    <div class="list-card__meta">
                        <strong>{{ $customer->license_verified ? 'Verified' : 'Pending review' }}</strong>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Recommended vehicles</span>
                <h2>Available EVs with strong charge levels</h2>
            </div>
        </div>
        <div class="cards-grid cards-grid--vehicles">
            @foreach ($recommendedVehicles as $vehicle)
                @include('partials.vehicle-card', ['vehicle' => $vehicle])
            @endforeach
        </div>
    </section>

    <section class="panel">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Charging network</span>
                <h2>Stations to keep your trip comfortable</h2>
            </div>
        </div>
        <div class="stack-list">
            @foreach ($stationHighlights as $station)
                <article class="list-card">
                    <div>
                        <h3>{{ $station->name }}</h3>
                        <p>{{ $station->zone }} &middot; {{ $station->connector_type }} &middot; {{ $station->confidence_summary }}</p>
                    </div>
                    <div class="list-card__meta">
                        <strong>{{ $station->risk_label }}</strong>
                        <span>{{ $station->live_ports }}/{{ $station->total_ports }} ports &middot; PHP {{ number_format((float) $station->price_per_kwh, 2) }}/kWh</span>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endsection
