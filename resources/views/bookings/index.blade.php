@extends('layouts.app')

@section('title', 'ECROS Bookings')

@section('content')
    <section class="section-heading section-heading--intro">
        <div>
            <span class="eyebrow">{{ $viewer->isAdmin() ? 'Reservation records' : 'My trips' }}</span>
            <h1>
                {{ $viewer->isAdmin()
                    ? 'Track customer trips, payment totals, and booking status from one protected view.'
                    : 'Review your EV bookings, planned trip windows, and booking totals in one place.' }}
            </h1>
            <p class="lead">
                {{ $viewer->isAdmin()
                    ? 'Admins keep full visibility into bookings across the fleet.'
                    : 'This page is scoped to your signed-in customer account only.' }}
            </p>
        </div>
        @if ($viewer->isCustomer())
            <a class="btn btn-primary" href="{{ route('bookings.create') }}">Create booking</a>
        @endif
    </section>

    <section class="metric-grid">
        <div class="metric-card">
            <span>Total bookings</span>
            <strong>{{ $summary['total'] }}</strong>
        </div>
        <div class="metric-card">
            <span>Confirmed</span>
            <strong>{{ $summary['confirmed'] }}</strong>
        </div>
        <div class="metric-card">
            <span>Active</span>
            <strong>{{ $summary['active'] }}</strong>
        </div>
        <div class="metric-card">
            <span>{{ $viewer->isAdmin() ? 'Revenue' : 'Trip spend' }}</span>
            <strong>PHP {{ number_format((float) $summary['value'], 2) }}</strong>
        </div>
    </section>

    <section class="stack-list">
        @forelse ($bookings as $booking)
            <article class="panel booking-card">
                <div class="booking-card__header">
                    <div>
                        <span class="eyebrow">Trip {{ $booking->reference }}</span>
                        <h2>{{ $viewer->isAdmin() ? $booking->user->name : $booking->vehicle->name }}</h2>
                        <p class="lead">
                            {{ $viewer->isAdmin() ? $booking->vehicle->name : $booking->pickup_location.' to '.$booking->dropoff_location }}
                            &middot; {{ ucfirst($booking->status) }}
                        </p>
                    </div>
                    <div class="booking-card__price">
                        <strong>PHP {{ number_format((float) $booking->total_cost, 2) }}</strong>
                        <span>{{ $booking->estimated_distance_km }} km planned</span>
                    </div>
                </div>

                <div class="metric-grid metric-grid--compact">
                    <div class="metric-card">
                        <span>Pickup</span>
                        <strong>{{ $booking->pickup_location }}</strong>
                    </div>
                    <div class="metric-card">
                        <span>Drop-off</span>
                        <strong>{{ $booking->dropoff_location }}</strong>
                    </div>
                    <div class="metric-card">
                        <span>Starts</span>
                        <strong>{{ $booking->start_at->format('M d, h:i A') }}</strong>
                    </div>
                    <div class="metric-card">
                        <span>Ends</span>
                        <strong>{{ $booking->end_at->format('M d, h:i A') }}</strong>
                    </div>
                </div>
            </article>
        @empty
            <div class="panel empty-state">
                <h2>No bookings yet</h2>
                <p>{{ $viewer->isAdmin() ? 'No fleet reservations are currently seeded.' : 'Create your first EV booking to populate this view.' }}</p>
            </div>
        @endforelse
    </section>
@endsection
