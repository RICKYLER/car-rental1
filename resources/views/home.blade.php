@extends('layouts.app')

@section('title', 'ECROS Overview')

@section('content')
    @php
        $spotlightBooking = $recentBookings->first();
        $spotlightStation = $stationHighlights->first();
    @endphp

    <section class="hero hero--home">
        <div class="hero__copy panel panel--accent">
            <span class="eyebrow">Customer mobile experience</span>
            <h1>Find a charged EV, book fast, and keep every trip feeling premium.</h1>
            <p class="lead">
                ECROS turns electric rentals into a cleaner customer journey with live battery visibility,
                range-aware matching, and booking details that feel closer to a polished app than an admin tool.
            </p>
            <div class="hero__actions">
                @auth
                    <a class="btn btn-primary" href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('bookings.create') }}">
                        {{ auth()->user()->isAdmin() ? 'Open admin board' : 'Book an EV' }}
                    </a>
                    <a class="btn btn-secondary" href="{{ auth()->user()->isAdmin() ? route('bookings.index') : route('dashboard') }}">
                        {{ auth()->user()->isAdmin() ? 'Review trips' : 'Open dashboard' }}
                    </a>
                @else
                    <a class="btn btn-primary" href="{{ route('login') }}">Log in to book</a>
                    <a class="btn btn-secondary" href="{{ route('register') }}">Create customer account</a>
                @endauth
            </div>
            <div class="hero-pill-row">
                <span class="hero-pill">Battery-safe trip planning</span>
                <span class="hero-pill">Transparent pricing</span>
                <span class="hero-pill">Live charging availability</span>
            </div>
        </div>

        <div class="hero__stats">
            <div class="panel panel--dark showcase-card">
                <span class="eyebrow eyebrow--dark">Live telemetry</span>
                <h2>Customer app snapshot</h2>
                <div class="metric-grid">
                    @foreach ($metrics as $metric)
                        <div class="metric-card metric-card--dark">
                            <span>{{ $metric['label'] }}</span>
                            <strong>{{ $metric['value'] }}</strong>
                            <small>{{ $metric['caption'] }}</small>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="panel quick-panel">
                <div class="quick-panel__header">
                    <span class="eyebrow">Ready right now</span>
                    <strong>Plan a smooth pickup</strong>
                </div>

                @if ($spotlightBooking)
                    <article class="list-card">
                        <div>
                            <h3>{{ $spotlightBooking->reference }}</h3>
                            <p>{{ $spotlightBooking->user->name }} &middot; {{ $spotlightBooking->vehicle->name }}</p>
                        </div>
                        <div class="list-card__meta">
                            <strong>{{ ucfirst($spotlightBooking->status) }}</strong>
                            <span>PHP {{ number_format((float) $spotlightBooking->total_cost, 2) }}</span>
                        </div>
                    </article>
                @endif

                @if ($spotlightStation)
                    <article class="list-card">
                        <div>
                            <h3>{{ $spotlightStation->name }}</h3>
                            <p>{{ $spotlightStation->zone }} &middot; {{ $spotlightStation->connector_type }} &middot; {{ $spotlightStation->confidence_summary }}</p>
                        </div>
                        <div class="list-card__meta">
                            <strong>{{ $spotlightStation->risk_label }}</strong>
                            <span>{{ $spotlightStation->live_ports }}/{{ $spotlightStation->total_ports }} ports &middot; {{ number_format((float) $spotlightStation->distance_from_hub_km, 1) }} km away</span>
                        </div>
                    </article>
                @endif
            </div>
        </div>
    </section>

    <section class="mode-grid">
        <article class="panel mode-card mode-card--light">
            <span class="mode-card__icon">01</span>
            <span class="eyebrow">Customer app</span>
            <h2>Browse, compare, and reserve in one calm flow.</h2>
            <p class="lead">
                Ideal for guests who want a polished mobile-style experience with clear battery status, range, and pricing.
            </p>
            <ul class="feature-points">
                <li>Friction-light booking form</li>
                <li>Vehicle-first browsing</li>
                <li>Trip-safe range context</li>
            </ul>
            <a class="btn btn-primary" href="{{ auth()->check() ? route('dashboard') : route('register') }}">
                {{ auth()->check() ? 'Open customer dashboard' : 'Launch customer flow' }}
            </a>
        </article>

        <article class="panel panel--dark mode-card">
            <span class="mode-card__icon mode-card__icon--dark">02</span>
            <span class="eyebrow eyebrow--dark">Admin dashboard</span>
            <h2>Monitor fleet health, charging, and live bookings.</h2>
            <p class="lead">
                A contrasting operations layer for dispatch, maintenance, and charging oversight without losing the cleaner visual system.
            </p>
            <ul class="feature-points feature-points--dark">
                <li>Telemetry-driven alerts</li>
                <li>Charging queue visibility</li>
                <li>Fleet readiness tracking</li>
            </ul>
            <a class="btn btn-primary btn-primary--dark" href="{{ route('admin.dashboard') }}">Open admin board</a>
        </article>
    </section>

    <section class="panel section">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Why it feels better</span>
                <h2>Customer-centered features with a more app-like tone</h2>
            </div>
        </div>
        <div class="feature-grid">
            @foreach ($operations as $operation)
                <article class="feature-card">
                    <h3>{{ $operation['title'] }}</h3>
                    <p>{{ $operation['copy'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Top picks</span>
                <h2>Featured EVs with strong charge and clean availability</h2>
            </div>
            <a class="btn btn-secondary" href="{{ route('fleet.index') }}">See all vehicles</a>
        </div>
        <div class="cards-grid cards-grid--vehicles">
            @foreach ($featuredVehicles as $vehicle)
                @include('partials.vehicle-card', ['vehicle' => $vehicle])
            @endforeach
        </div>
    </section>

    <section class="section section--split">
        <div class="panel">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Charging network</span>
                    <h2>Nearby stations that fit the customer journey</h2>
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
        </div>

        <div class="panel panel--dark">
            <div class="section-heading">
                <div>
                    <span class="eyebrow eyebrow--dark">Recent activity</span>
                    <h2>Sample bookings with a polished trip summary</h2>
                </div>
                <a class="btn btn-secondary" href="{{ route('bookings.index') }}">All bookings</a>
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
        </div>
    </section>
@endsection
