@php
    $statusClass = match ($vehicle->status) {
        'available' => 'status-available',
        'charging' => 'status-charging',
        'reserved' => 'status-reserved',
        default => 'status-maintenance',
    };

    $rangeProgress = max(min((int) round(($vehicle->battery_soc / 100) * 100), 100), 12);
@endphp

<article class="vehicle-card" style="--vehicle-accent: {{ $vehicle->accent_color }}">
    <div class="vehicle-card__visual">
        <div class="vehicle-card__visual-top">
            <span class="status-pill {{ $statusClass }}">{{ ucfirst($vehicle->status) }}</span>
            <span class="vehicle-card__charge">{{ $vehicle->battery_soc }}%</span>
        </div>

        <div class="vehicle-card__badge">
            <strong>{{ $vehicle->brand }}</strong>
            <span>{{ $vehicle->model }} &middot; {{ $vehicle->connector_type }}</span>
        </div>

        <div class="vehicle-card__shape"></div>
    </div>

    <div class="vehicle-card__body">
        <div class="vehicle-card__header">
            <div>
                <h3>{{ $vehicle->name }}</h3>
                <p>{{ $vehicle->location_zone }} &middot; {{ $vehicle->estimated_range_km }} km estimated range</p>
            </div>
            <strong class="vehicle-card__price">PHP {{ number_format((float) $vehicle->daily_rate, 0) }}/day</strong>
        </div>

        <div class="vehicle-card__meter">
            <div class="vehicle-card__meter-copy">
                <span>Battery confidence</span>
                <strong>{{ $vehicle->battery_health }}% health</strong>
            </div>
            <div class="meter">
                <span class="meter__fill" style="width: {{ $rangeProgress }}%"></span>
            </div>
        </div>

        <div class="metric-grid metric-grid--compact">
            <div class="metric-card">
                <span>Rate / km</span>
                <strong>PHP {{ number_format((float) $vehicle->per_km_rate, 2) }}</strong>
            </div>
            <div class="metric-card">
                <span>Energy</span>
                <strong>PHP {{ number_format((float) $vehicle->energy_rate, 2) }}/kWh</strong>
            </div>
        </div>

        <p class="vehicle-card__copy">{{ $vehicle->description }}</p>

        <div class="vehicle-card__actions">
            <a class="btn btn-secondary" href="{{ route('fleet.show', $vehicle) }}">View details</a>
            @if ($vehicle->status === 'available')
                <a class="btn btn-primary" href="{{ route('bookings.create', ['vehicle' => $vehicle->id]) }}">Book this EV</a>
            @else
                <span class="muted-note">Unavailable for a new trip right now</span>
            @endif
        </div>
    </div>
</article>
