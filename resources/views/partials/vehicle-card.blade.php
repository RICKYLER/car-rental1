@php
    $statusClass = match ($vehicle->status) {
        'available' => 'status-available',
        'charging' => 'status-charging',
        'reserved' => 'status-reserved',
        default => 'status-maintenance',
    };

    $rangeProgress = max(min((int) round(($vehicle->battery_soc / 100) * 100), 100), 12);
    $telemetry = $vehicle->telemetry_summary ?? null;
    $displayRange = $telemetry['range_km'] ?? $vehicle->estimated_range_km;
    $freshnessLabel = $telemetry['freshness_label'] ?? 'Live';
    $confidenceTone = $telemetry['confidence_tone'] ?? 'good';
@endphp

<article class="vehicle-card" style="--vehicle-accent: {{ $vehicle->accent_color }}">
    <div class="vehicle-card__visual">
        <div class="vehicle-card__visual-top">
            <span class="status-pill {{ $statusClass }}">{{ ucfirst($vehicle->status) }}</span>
            <div class="vehicle-card__telemetry-top">
                <span class="badge badge--{{ $confidenceTone }}">{{ $freshnessLabel }}</span>
                <span class="vehicle-card__charge">{{ $vehicle->battery_soc }}%</span>
            </div>
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
                <p>{{ $vehicle->location_zone }} &middot; {{ $displayRange }} km {{ strtolower($freshnessLabel) }} range</p>
            </div>
            <strong class="vehicle-card__price">PHP {{ number_format((float) $vehicle->daily_rate, 0) }}/day</strong>
        </div>

        <div class="vehicle-card__meter">
            <div class="vehicle-card__meter-copy">
                <span>Battery confidence</span>
                <strong>{{ $vehicle->battery_health }}% health / {{ $freshnessLabel }} feed</strong>
            </div>
            <div class="meter">
                <span class="meter__fill" style="width: {{ $rangeProgress }}%"></span>
            </div>
        </div>

        @if ($telemetry)
            <div class="telemetry-pills">
                <span class="signal-pill signal-pill--{{ $confidenceTone }}">{{ $telemetry['signal_label'] }}</span>
                @if ($telemetry['observed_at'])
                    <span class="signal-pill">Updated {{ $telemetry['observed_at']->diffForHumans() }}</span>
                @endif
            </div>
        @endif

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
