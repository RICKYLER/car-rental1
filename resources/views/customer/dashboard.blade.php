@extends('layouts.app')

@section('title', 'Customer Portal | ECROS')

@section('content')
    <div style="display: grid; gap: 32px;">
        {{-- Elegant Welcome Header --}}
        <header style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); padding: 64px; border-radius: 32px; border: 1px solid #bbf7d0; position: relative; overflow: hidden;">
            <div style="position: relative; z-index: 10; max-width: 600px;">
                <span class="eyebrow" style="background: rgba(22, 101, 52, 0.1); color: #166534; margin-bottom: 16px;">Welcome Back</span>
                <h1 style="color: #166534; font-size: clamp(2rem, 4vw, 3rem); line-height: 1.1; margin-bottom: 16px;">Hello, {{ explode(' ', $customer->name)[0] }}.</h1>
                <p style="color: #15803d; font-size: 1.1rem; margin-bottom: 32px; opacity: 0.8;">
                    Ready for your next sustainable journey? Your fleet is charged and waiting.
                </p>
                <div style="display: flex; gap: 12px;">
                    <a href="{{ route('fleet.index') }}" class="btn btn-primary" style="background: #166534; border: none;">Explore Fleet</a>
                    <a href="{{ route('bookings.index') }}" class="btn btn-secondary" style="border-color: #bbf7d0; color: #166534;">View My Trips</a>
                </div>
            </div>
            <div style="position: absolute; right: -40px; bottom: -40px; width: 300px; height: 300px; background: radial-gradient(circle, rgba(34, 197, 94, 0.1) 0%, transparent 70%); border-radius: 999px;"></div>
        </header>

        <div class="metric-grid">
            <article class="metric-card" style="border-color: #dcfce7;">
                <div class="metric-card__icon" style="background: #f0fdf4; color: #166534;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                </div>
                <div class="metric-card__body">
                    <span>Total Journeys</span>
                    <strong>{{ $summary['bookings'] }} Trips</strong>
                </div>
            </article>

            <article class="metric-card" style="border-color: #dcfce7;">
                <div class="metric-card__icon" style="background: #f0fdf4; color: #166534;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.5 21 2c-2 5-2.5 7-3.4 13.2A7 7 0 0 1 11 20z"/></svg>
                </div>
                <div class="metric-card__body">
                    <span>CO2 Impact</span>
                    <strong>{{ $summary['bookings'] * 1.2 }}kg Saved</strong>
                </div>
            </article>

            <article class="metric-card" style="border-color: #dcfce7;">
                <div class="metric-card__icon" style="background: #f0fdf4; color: #166534;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2v4"/><path d="M12 18v4"/><path d="M4.93 4.93l2.83 2.83"/><path d="M16.24 16.24l2.83 2.83"/><path d="M2 12h4"/><path d="M18 12h4"/><path d="M4.93 19.07l2.83-2.83"/><path d="M16.24 7.76l2.83-2.83"/></svg>
                </div>
                <div class="metric-card__body">
                    <span>Eco Score</span>
                    <strong>94 / 100</strong>
                </div>
            </article>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px;">
            <div class="panel" style="padding: 0; overflow: hidden; border-color: #dcfce7;">
                <div style="padding: 24px; border-bottom: 1px solid #f0fdf4; display: flex; justify-content: space-between; align-items: center;">
                    <h2 style="font-size: 1.25rem; color: #166534;">Nearby Vehicles</h2>
                    <a href="{{ route('fleet.index') }}" class="text-link" style="color: #166534; font-size: 0.88rem;">View Full Fleet</a>
                </div>
                <div id="customer-nearby-map" style="height: 440px; background: #f0fdf4; position: relative; z-index: 1;"></div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const map = L.map('customer-nearby-map', {
                            center: [7.0731, 125.6111],
                            zoom: 13,
                            zoomControl: false
                        });

                        L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                            maxZoom: 20,
                            subdomains:['mt0','mt1','mt2','mt3'],
                            attribution: '&copy; Google Maps'
                        }).addTo(map);

                        const vehicles = @json($recommendedVehicles);
                        
                        vehicles.forEach(vehicle => {
                            const markerHtml = `
                                <div style="width: 40px; height: 40px; background: #166534; border: 4px solid #fff; border-radius: 999px; box-shadow: var(--shadow-lg); display: grid; place-items: center; color: #fff; cursor: pointer;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                                </div>
                            `;

                            const customIcon = L.divIcon({
                                html: markerHtml,
                                className: 'custom-div-icon',
                                iconSize: [40, 40],
                                iconAnchor: [20, 20]
                            });

                            const lat = 7.0731 + (Math.random() - 0.5) * 0.04;
                            const lng = 125.6111 + (Math.random() - 0.5) * 0.04;

                            L.marker([lat, lng], {icon: customIcon})
                                .addTo(map)
                                .bindPopup(`<strong>${vehicle.name}</strong><br>Ready to book!`);
                        });
                    });
                </script>
            </div>

            <div style="display: grid; gap: 32px; align-content: start;">
                <div class="panel" style="background: #166534; color: #fff; border: none;">
                    <h2 style="font-size: 1.1rem; margin-bottom: 24px;">Current Status</h2>
                    @forelse ($upcomingTrips as $booking)
                        <div style="background: rgba(255,255,255,0.1); padding: 24px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1);">
                            <span class="badge" style="background: rgba(34, 197, 94, 0.4); color: #fff; border: none; margin-bottom: 12px;">Upcoming Trip</span>
                            <strong style="display: block; font-size: 1.25rem; margin-bottom: 8px;">{{ $booking->vehicle->name }}</strong>
                            <p style="font-size: 0.94rem; opacity: 0.8; margin-bottom: 20px;">{{ $booking->start_at->format('l, M d') }} at {{ $booking->start_at->format('h:i A') }}</p>
                            <a href="{{ route('bookings.index') }}" class="btn btn-secondary" style="width: 100%; border-color: rgba(255,255,255,0.2); color: #fff;">Manage Trip</a>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 24px 0;">
                            <p style="opacity: 0.7; margin-bottom: 20px;">No active trips at the moment.</p>
                            <a href="{{ route('fleet.index') }}" class="btn btn-secondary" style="width: 100%; border-color: rgba(255,255,255,0.2); color: #fff;">Book Now</a>
                        </div>
                    @endforelse
                </div>

                <div class="panel" style="border-color: #dcfce7;">
                    <h2 style="font-size: 1.1rem; color: #166534; margin-bottom: 16px;">Quick Tip</h2>
                    <p style="font-size: 0.88rem; color: #15803d; line-height: 1.6;">
                        Using <strong>Eco-Mode</strong> on your Tesla Model 3 can extend your range by up to 15km in city traffic.
                    </p>
                </div>
            </div>
        </div>

        <section>
            <div class="section-heading">
                <div>
                    <h2 style="color: #166534;">Recommended for you</h2>
                    <p style="color: #15803d;">Top-rated EVs near your current zone</p>
                </div>
                <a href="{{ route('fleet.index') }}" class="text-link" style="color: #166534;">View Full Fleet &rarr;</a>
            </div>
            <div class="cards-grid--vehicles">
                @foreach ($recommendedVehicles->take(3) as $vehicle)
                    @include('partials.vehicle-card', ['vehicle' => $vehicle])
                @endforeach
            </div>
        </section>
    </div>
@endsection
