@extends('layouts.app')

@section('title', 'Welcome | ECROS')

@section('content')
    <header style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 64px; border-radius: 32px; color: #fff; position: relative; overflow: hidden; margin-bottom: 32px;">
        <div style="position: relative; z-index: 10; max-width: 800px;">
            <span class="eyebrow" style="background: rgba(255,255,255,0.1); color: #fff; margin-bottom: 16px;">Platform Overview</span>
            <h1 style="font-size: clamp(2.5rem, 5vw, 4rem); line-height: 1.1; margin-bottom: 24px;">Sustainable mobility, managed intelligently.</h1>
            <p style="font-size: 1.25rem; opacity: 0.8; margin-bottom: 32px; line-height: 1.6;">
                ECROS provides a seamless bridge between electric vehicle fleet operations and premium customer rental experiences.
            </p>
            <div style="display: flex; gap: 16px;">
                <a href="{{ route('fleet.index') }}" class="btn btn-primary" style="background: #fff; color: #0f172a;">Explore Fleet</a>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary" style="border-color: rgba(255,255,255,0.2); color: #fff;">Admin Command</a>
            </div>
        </div>
        <div style="position: absolute; right: -50px; bottom: -50px; width: 400px; height: 400px; background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, transparent 70%); border-radius: 999px;"></div>
    </header>

    <div class="metric-grid" style="margin-bottom: 48px;">
        @foreach ($metrics as $index => $metric)
            <div class="metric-card">
                <div class="metric-card__icon" style="background: {{ ['#eff6ff', '#f0fdf4', '#fdf4ff', '#fff7ed'][$index] ?? 'var(--primary-soft)' }}; color: {{ ['#3b82f6', '#22c55e', '#d946ef', '#f97316'][$index] ?? 'var(--primary)' }};">
                    @switch($index)
                        @case(0) {{-- Fleet Ready --}}
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                            @break
                        @case(1) {{-- Avg Battery --}}
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="10" x="2" y="7" rx="2"/><line x1="22" x2="22" y1="11" y2="13"/><line x1="6" x2="6" y1="7" y2="17"/></svg>
                            @break
                        @case(2) {{-- Active Journeys --}}
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                            @break
                        @case(3) {{-- CO2 Avoided --}}
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.5 21 2c-2 5-2.5 7-3.4 13.2A7 7 0 0 1 11 20z"/></svg>
                            @break
                    @endswitch
                </div>
                <div class="metric-card__body">
                    <span>{{ $metric['label'] }}</span>
                    <strong>{{ $metric['value'] }}</strong>
                    <div style="font-size: 0.82rem; color: var(--muted); line-height: 1.4;">{{ $metric['caption'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <section style="margin-bottom: 64px;">
        <div class="section-heading">
            <div>
                <h2>Dual-Mode Experience</h2>
                <p style="color: var(--muted);">Choose your interface based on your operational needs.</p>
            </div>
        </div>
        <div class="feature-grid">
            <article class="feature-card" style="background: #fff; border-color: #e2e8f0;">
                <div class="metric-card__icon" style="background: #f0fdf4; color: #166534; margin-bottom: 24px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                </div>
                <h3>Customer Portal</h3>
                <p style="margin-bottom: 24px;">A calm, map-first experience designed for finding and booking electric vehicles with ease.</p>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary" style="width: 100%;">Launch Customer App</a>
            </article>

            <article class="feature-card" style="background: #0f172a; border-color: rgba(255,255,255,0.1); color: #fff;">
                <div class="metric-card__icon" style="background: rgba(255,255,255,0.1); color: #fff; margin-bottom: 24px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3 style="color: #fff;">Operations Command</h3>
                <p style="opacity: 0.7; margin-bottom: 24px;">High-density telemetry, charging queue management, and real-time fleet health monitoring.</p>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary" style="width: 100%;">Enter Admin Board</a>
            </article>
        </div>
    </section>

    <section>
        <div class="section-heading">
            <div>
                <h2>Featured Fleet</h2>
                <p style="color: var(--muted);">Top-tier electric vehicles ready for deployment.</p>
            </div>
            <a href="{{ route('fleet.index') }}" class="text-link">View Full Fleet &rarr;</a>
        </div>
        <div class="cards-grid--vehicles">
            @foreach ($featuredVehicles->take(3) as $vehicle)
                @include('partials.vehicle-card', ['vehicle' => $vehicle])
            @endforeach
        </div>
    </section>
@endsection
