@extends('layouts.app')

@section('title', 'ECROS Fleet')

@section('content')
    @php
        $availableCount = $vehicles->where('status', 'available')->count();
        $averageBattery = $vehicles->isNotEmpty() ? (int) round((float) $vehicles->avg('battery_soc')) : 0;
        $zonesCovered = $vehicles->pluck('location_zone')->unique()->count();
    @endphp

    <section class="section-heading section-heading--intro">
        <div>
            <span class="eyebrow">Customer fleet explorer</span>
            <h1>Choose the EV that fits your route, battery needs, and pickup zone.</h1>
            <p class="lead">
                A cleaner discovery experience with fast filters, booking-ready cards, and the details customers actually need.
            </p>
        </div>
        <a class="btn btn-primary" href="{{ route('bookings.create') }}">Start booking</a>
    </section>

    <section class="section section--split section--overview">
        <div class="panel panel--accent">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Filters</span>
                    <h2>Refine the live fleet</h2>
                </div>
            </div>

            <form method="GET" action="{{ route('fleet.index') }}" class="filter-grid">
                <label>
                    <span>Status</span>
                    <select name="status">
                        <option value="">All</option>
                        @foreach (['available', 'reserved', 'charging', 'maintenance'] as $status)
                            <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span>Connector</span>
                    <select name="connector">
                        <option value="">All</option>
                        @foreach ($connectors as $connector)
                            <option value="{{ $connector }}" @selected(($filters['connector'] ?? '') === $connector)>{{ $connector }}</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span>Zone</span>
                    <select name="zone">
                        <option value="">All</option>
                        @foreach ($zones as $zone)
                            <option value="{{ $zone }}" @selected(($filters['zone'] ?? '') === $zone)>{{ $zone }}</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span>Minimum battery</span>
                    <input type="number" min="0" max="100" name="min_soc" value="{{ $filters['min_soc'] ?? '' }}" placeholder="40">
                </label>

                <div class="filter-actions">
                    <button class="btn btn-primary" type="submit">Apply filters</button>
                    <a class="btn btn-secondary" href="{{ route('fleet.index') }}">Reset</a>
                </div>
            </form>
        </div>

        <div class="panel panel--dark">
            <div class="section-heading">
                <div>
                    <span class="eyebrow eyebrow--dark">Fleet summary</span>
                    <h2>Availability at a glance</h2>
                </div>
            </div>

            <div class="metric-grid metric-grid--compact">
                <div class="metric-card metric-card--dark">
                    <span>Shown now</span>
                    <strong>{{ $vehicles->count() }}</strong>
                </div>
                <div class="metric-card metric-card--dark">
                    <span>Ready to book</span>
                    <strong>{{ $availableCount }}</strong>
                </div>
                <div class="metric-card metric-card--dark">
                    <span>Avg. battery</span>
                    <strong>{{ $averageBattery }}%</strong>
                </div>
                <div class="metric-card metric-card--dark">
                    <span>Zones covered</span>
                    <strong>{{ $zonesCovered }}</strong>
                </div>
            </div>
        </div>
    </section>

    <section class="cards-grid cards-grid--vehicles">
        @forelse ($vehicles as $vehicle)
            @include('partials.vehicle-card', ['vehicle' => $vehicle])
        @empty
            <div class="panel empty-state">
                <h2>No vehicles matched the selected filters.</h2>
                <p>Try clearing the filters or lowering the minimum battery to see more options.</p>
            </div>
        @endforelse
    </section>
@endsection
