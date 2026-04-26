@extends('layouts.app')

@section('title', $vehicle->name)

@section('content')
    @php
        $latestBooking = $vehicle->bookings->first();
        $latestCharging = $vehicle->chargingSessions->first();
        $telemetry = $vehicle->telemetry_summary;

        $carImage = match (true) {
            str_contains(strtolower($vehicle->name), 'nissan leaf') => asset('images/vehicles/nissan-leaf.png'),
            str_contains(strtolower($vehicle->name), 'tesla model 3') => asset('images/vehicles/tesla-model-3.png'),
            str_contains(strtolower($vehicle->name), 'hyundai kona') => asset('images/vehicles/hyundai-kona.png'),
            str_contains(strtolower($vehicle->name), 'byd dolphin') => asset('images/vehicles/byd-dolphin.png'),
            str_contains(strtolower($vehicle->name), 'mg4') => asset('images/vehicles/mg4-electric.png'),
            default => 'https://images.unsplash.com/photo-1593941707882-a5bba14938c7?auto=format&fit=crop&q=80&w=600',
        };
    @endphp

    <div class="detail-hero-modern">
        <div class="detail-visual-panel" id="vehicle-visualizer">
            <div style="position: absolute; top: 24px; left: 24px; z-index: 10; display: flex; align-items: center; gap: 8px; background: rgba(0,0,0,0.6); color: #fff; padding: 8px 16px; border-radius: 999px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; backdrop-filter: blur(8px);">
                <span style="display: block; width: 8px; height: 8px; background: #22c55e; border-radius: 50%; box-shadow: 0 0 10px #22c55e;"></span>
                360° Interactive Visualizer
            </div>
            <img src="{{ $carImage }}" alt="{{ $vehicle->name }}" class="detail-car-image" id="interactive-car">
            <div style="position: absolute; bottom: 24px; color: var(--muted); font-size: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                Move mouse to rotate view
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
            </div>
        </div>

        <div class="detail-info-panel">
            <div class="glass-panel">
                <span class="eyebrow" style="margin-bottom: 12px;">Vehicle Profile</span>
                <h1 style="font-size: 3.2rem; line-height: 1; margin-bottom: 16px;">{{ $vehicle->name }}</h1>
                <p class="lead" style="margin-bottom: 32px; font-size: 1.1rem;">{{ $vehicle->description }}</p>

                <div class="metric-pill-grid">
                    <div class="metric-pill">
                        <div class="metric-pill__icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 12a11 11 0 1 1-22 0 11 11 0 0 1 22 0z"/><path d="m9 12 2 2 4-4"/></svg>
                        </div>
                        <div class="metric-pill__data">
                            <span>Battery</span>
                            <strong>{{ $vehicle->battery_soc }}%</strong>
                        </div>
                    </div>
                    <div class="metric-pill">
                        <div class="metric-pill__icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                        </div>
                        <div class="metric-pill__data">
                            <span>Range</span>
                            <strong>{{ $telemetry['range_km'] }} km</strong>
                        </div>
                    </div>
                    <div class="metric-pill">
                        <div class="metric-pill__icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <div class="metric-pill__data">
                            <span>Health</span>
                            <strong>{{ $vehicle->battery_health }}%</strong>
                        </div>
                    </div>
                    <div class="metric-pill">
                        <div class="metric-pill__icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </div>
                        <div class="metric-pill__data">
                            <span>Signal</span>
                            <strong>{{ $telemetry['freshness_label'] }}</strong>
                        </div>
                    </div>
                </div>

                <div class="hero__actions" style="margin-top: 40px;">
                    @if ($vehicle->status === 'available' && auth()->user()?->isCustomer())
                        <a class="btn btn-primary" href="{{ route('bookings.create', ['vehicle' => $vehicle->id]) }}" style="min-width: 200px; padding: 18px;">Book this vehicle</a>
                    @endif
                    <a class="btn btn-secondary" href="{{ route('fleet.index') }}" style="min-width: 200px; padding: 18px;">Back to fleet</a>
                </div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 32px; margin-bottom: 40px;">
        <div class="panel panel--dark" style="padding: 32px;">
            <span class="eyebrow eyebrow--dark" style="margin-bottom: 24px;">Pricing & Readiness</span>
            <div class="stack-list">
                <article class="list-card" style="background: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.08); padding: 24px;">
                    <div>
                        <h3 style="font-size: 1.1rem; margin-bottom: 4px;">Rental pricing</h3>
                        <p style="font-size: 0.9rem; opacity: 0.7;">Day rate plus transparent energy-aware trip costs</p>
                    </div>
                    <div class="list-card__meta">
                        <strong style="font-size: 1.2rem;">PHP {{ number_format((float) $vehicle->daily_rate, 0) }}/day</strong>
                        <span style="opacity: 0.6;">PHP {{ number_format((float) $vehicle->per_km_rate, 2) }}/km</span>
                    </div>
                </article>
                <article class="list-card" style="background: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.08); padding: 24px;">
                    <div>
                        <h3 style="font-size: 1.1rem; margin-bottom: 4px;">Current location</h3>
                        <p style="font-size: 0.9rem; opacity: 0.7;">{{ $telemetry['summary'] }}</p>
                    </div>
                    <div class="list-card__meta">
                        <strong style="font-size: 1.2rem;">{{ $vehicle->location_zone }}</strong>
                        <span style="opacity: 0.6;">{{ $vehicle->plate_number }}</span>
                    </div>
                </article>
            </div>
        </div>

        <div class="panel" style="padding: 32px; background: var(--bg-soft); border: none;">
            <span class="eyebrow" style="margin-bottom: 24px;">Maintenance</span>
            <div style="display: grid; gap: 20px;">
                <div style="text-align: center; padding: 24px; background: #fff; border-radius: 20px; border: 1px solid var(--line);">
                    <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: var(--muted); display: block; margin-bottom: 8px;">Next Service Due</span>
                    <strong style="font-size: 1.5rem; display: block;">{{ optional($vehicle->next_service_due_at)->format('M d, Y') ?? 'Jun 10, 2026' }}</strong>
                </div>
                <div style="padding: 16px; background: #fff; border-radius: 20px; border: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.88rem; color: var(--muted);">Odometer</span>
                    <strong style="font-size: 0.95rem;">{{ number_format($vehicle->odometer_km) }} km</strong>
                </div>
            </div>
        </div>
    </div>

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
                            <p>{{ $station->location }} &middot; {{ $station->confidence_summary }}</p>
                        </div>
                        <div class="list-card__meta">
                            <strong>{{ $station->live_ports }}/{{ $station->total_ports }} ports</strong>
                            <span>{{ $station->risk_label }} &middot; {{ number_format((float) $station->distance_from_hub_km, 1) }} km away</span>
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
    <script>
        const visualizer = document.getElementById('vehicle-visualizer');
        const car = document.getElementById('interactive-car');

        visualizer.addEventListener('mousemove', (e) => {
            const rect = visualizer.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 10;
            const rotateY = (centerX - x) / 10;
            
            car.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.1)`;
            car.style.filter = `drop-shadow(${rotateY}px ${rotateX}px 40px rgba(0,0,0,0.2))`;
        });

        visualizer.addEventListener('mouseleave', () => {
            car.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale(1)';
            car.style.filter = 'drop-shadow(0 20px 40px rgba(0,0,0,0.15))';
        });
    </script>
@endsection
