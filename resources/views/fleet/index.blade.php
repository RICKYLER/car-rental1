@extends('layouts.app')

@section('title', 'Fleet | ECROS')

@section('content')
    <div style="display: grid; gap: 32px;">
        <header style="display: flex; justify-content: space-between; align-items: flex-end;">
            <div>
                <span class="eyebrow" style="margin-bottom: 12px;">Fleet Explorer</span>
                <h1 style="margin: 0;">Find your next electric trip.</h1>
                <p style="color: var(--muted); font-size: 1.1rem; margin-top: 8px;">Filter by battery, zone, or connector type to get moving.</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <div class="metric-card" style="padding: 12px 20px; min-width: 0;">
                    <div style="text-align: center;">
                        <span style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700;">Ready</span>
                        <strong style="display: block; font-size: 1.25rem;">{{ $vehicles->where('status', 'available')->count() }}</strong>
                    </div>
                </div>
                <div class="metric-card" style="padding: 12px 20px; min-width: 0;">
                    <div style="text-align: center;">
                        <span style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700;">Avg. SoC</span>
                        <strong style="display: block; font-size: 1.25rem;">{{ (int) round((float) $vehicles->avg('battery_soc')) }}%</strong>
                    </div>
                </div>
            </div>
        </header>

        <section class="panel" style="background: var(--bg-soft); border: none;">
            <form method="GET" action="{{ route('fleet.index') }}" style="display: flex; gap: 16px; align-items: flex-end;">
                <div style="flex: 1; display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
                    <label>
                        <span>Status</span>
                        <select name="status" style="width: 100%; padding: 12px; border-radius: 12px; border: 1px solid var(--line);">
                            <option value="">All Status</option>
                            @foreach (['available', 'reserved', 'charging', 'maintenance'] as $status)
                                <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span>Zone</span>
                        <select name="zone" style="width: 100%; padding: 12px; border-radius: 12px; border: 1px solid var(--line);">
                            <option value="">All Zones</option>
                            @foreach ($zones as $zone)
                                <option value="{{ $zone }}" @selected(($filters['zone'] ?? '') === $zone)>{{ $zone }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span>Connector</span>
                        <select name="connector" style="width: 100%; padding: 12px; border-radius: 12px; border: 1px solid var(--line);">
                            <option value="">All Connectors</option>
                            @foreach ($connectors as $connector)
                                <option value="{{ $connector }}" @selected(($filters['connector'] ?? '') === $connector)>{{ $connector }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span>Min. Battery</span>
                        <input type="number" name="min_soc" value="{{ $filters['min_soc'] ?? '' }}" placeholder="e.g. 50" style="width: 100%; padding: 12px; border-radius: 12px; border: 1px solid var(--line);">
                    </label>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn btn-primary" style="min-width: 0; padding-inline: 24px;">Filter</button>
                    <a href="{{ route('fleet.index') }}" class="btn btn-secondary" style="min-width: 0; padding-inline: 24px;">Reset</a>
                </div>
            </form>
        </section>

        <div class="cards-grid--vehicles">
            @forelse ($vehicles as $vehicle)
                @include('partials.vehicle-card', ['vehicle' => $vehicle])
            @empty
                <div class="panel" style="grid-column: 1 / -1; padding: 64px; text-align: center;">
                    <h2 style="color: var(--muted);">No vehicles found matching your criteria.</h2>
                    <a href="{{ route('fleet.index') }}" class="text-link" style="margin-top: 16px; display: inline-block;">Clear all filters</a>
                </div>
            @endforelse
        </div>
    </div>
@endsection
