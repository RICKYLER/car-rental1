@php
    $statusClass = match ($vehicle->status) {
        'available' => 'status-available',
        'charging' => 'status-charging',
        'reserved' => 'status-reserved',
        default => 'status-maintenance',
    };

    $carImage = match (true) {
        str_contains(strtolower($vehicle->name), 'nissan leaf') => asset('images/vehicles/nissan-leaf.png'),
        str_contains(strtolower($vehicle->name), 'tesla model 3') => asset('images/vehicles/tesla-model-3.png'),
        str_contains(strtolower($vehicle->name), 'hyundai kona') => asset('images/vehicles/hyundai-kona.png'),
        str_contains(strtolower($vehicle->name), 'byd dolphin') => asset('images/vehicles/byd-dolphin.png'),
        str_contains(strtolower($vehicle->name), 'mg4') => asset('images/vehicles/mg4-electric.png'),
        default => 'https://images.unsplash.com/photo-1593941707882-a5bba14938c7?auto=format&fit=crop&q=80&w=600',
    };
@endphp

<article class="vehicle-card">
    <div class="vehicle-card__visual" style="background-image: url('{{ $carImage }}'); background-size: cover; background-position: center;">
        <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(0,0,0,0.4) 0%, transparent 40%, rgba(0,0,0,0.6) 100%);"></div>
        
        <div style="position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="status-pill {{ $statusClass }}">{{ ucfirst($vehicle->status) }}</span>
            <div style="background: rgba(255,255,255,0.9); padding: 4px 12px; border-radius: 999px; font-weight: 800; font-size: 0.82rem; color: #0f172a;">
                {{ $vehicle->battery_soc }}% Charge
            </div>
        </div>

        <div style="position: absolute; bottom: 20px; left: 24px; z-index: 2; color: #fff;">
            <strong style="font-size: 1.25rem; display: block;">{{ $vehicle->name }}</strong>
            <span style="font-size: 0.88rem; opacity: 0.9;">{{ $vehicle->brand }} {{ $vehicle->model }}</span>
        </div>
    </div>

    <div class="vehicle-card__body">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; padding-bottom: 20px; border-bottom: 1px solid var(--line);">
            <div style="text-align: center;">
                <span style="display: block; font-size: 0.65rem; text-transform: uppercase; color: var(--muted); font-weight: 700; margin-bottom: 4px;">Range</span>
                <strong style="font-size: 0.95rem;">{{ $vehicle->estimated_range_km }}km</strong>
            </div>
            <div style="text-align: center; border-inline: 1px solid var(--line);">
                <span style="display: block; font-size: 0.65rem; text-transform: uppercase; color: var(--muted); font-weight: 700; margin-bottom: 4px;">Type</span>
                <strong style="font-size: 0.95rem;">{{ $vehicle->connector_type }}</strong>
            </div>
            <div style="text-align: center;">
                <span style="display: block; font-size: 0.65rem; text-transform: uppercase; color: var(--muted); font-weight: 700; margin-bottom: 4px;">Rate</span>
                <strong style="font-size: 0.95rem;">₱{{ number_format($vehicle->daily_rate, 0) }}</strong>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
            <div style="display: flex; align-items: center; gap: 8px; color: var(--muted); font-size: 0.82rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ $vehicle->location_zone }}
            </div>
            <a href="{{ route('fleet.show', $vehicle) }}" class="btn btn-primary" style="min-width: 0; padding: 10px 20px; font-size: 0.82rem;">
                @if($vehicle->status === 'available' && auth()->user()?->isCustomer())
                    Book Now
                @else
                    Details
                @endif
            </a>
        </div>
    </div>
</article>
