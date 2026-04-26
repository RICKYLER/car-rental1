@extends('layouts.app')

@section('title', 'Admin Dashboard | ECROS')

@section('content')
    <div style="display: grid; gap: 32px;">
        {{-- Header Stats Row --}}
        <div class="metric-grid">
            <article class="metric-card">
                <div class="metric-card__icon" style="background: #eff6ff; color: #3b82f6;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                </div>
                <div class="metric-card__body">
                    <span>Active Cars</span>
                    <strong>48</strong>
                    <div class="metric-card__trend trend--up">+12%</div>
                </div>
            </article>

            <article class="metric-card">
                <div class="metric-card__icon" style="background: #f0fdf4; color: #22c55e;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/></svg>
                </div>
                <div class="metric-card__body">
                    <span>Ongoing Trips</span>
                    <strong>32</strong>
                    <div class="metric-card__trend trend--up">+5%</div>
                </div>
            </article>

            <article class="metric-card">
                <div class="metric-card__icon" style="background: #fef2f2; color: #ef4444;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
                </div>
                <div class="metric-card__body">
                    <span>Maintenance</span>
                    <strong>3 Alerts</strong>
                    <div class="metric-card__trend trend--down">Action Req</div>
                </div>
            </article>

            <article class="metric-card">
                <div class="metric-card__icon" style="background: #fff7ed; color: #f97316;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <div class="metric-card__body">
                    <span>Revenue</span>
                    <strong>₱{{ number_format($stats['revenue'], 0) }}</strong>
                    <div class="metric-card__trend trend--up">+18%</div>
                </div>
            </article>
        </div>

        {{-- Main Dashboard Grid --}}
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px;">
            {{-- Fleet Map --}}
            <div class="panel" style="padding: 0; overflow: hidden;">
                <div style="padding: 24px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center;">
                    <h2 style="font-size: 1.25rem;">Live Fleet Map</h2>
                    <div style="display: flex; gap: 8px;">
                        <button class="badge" style="background: var(--bg-soft); border: none; cursor: pointer;">Satellite</button>
                        <button class="badge" style="background: var(--primary-soft); color: var(--primary); border: none; cursor: pointer;">Street</button>
                    </div>
                </div>
                <div id="dashboard-map" style="height: 500px; background: #f1f5f9; position: relative; z-index: 1;"></div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const map = L.map('dashboard-map', {
                            center: [7.0731, 125.6111],
                            zoom: 12,
                            zoomControl: false
                        });

                        L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                            maxZoom: 20,
                            subdomains:['mt0','mt1','mt2','mt3'],
                            attribution: '&copy; Google Maps'
                        }).addTo(map);

                        const vehicles = @json($vehicles->take(5));
                        
                        vehicles.forEach(vehicle => {
                            const lat = 7.0731 + (Math.random() - 0.5) * 0.04;
                            const lng = 125.6111 + (Math.random() - 0.5) * 0.04;

                            const markerHtml = `
                                <div style="background: ${vehicle.status === 'available' ? '#22c55e' : '#3b82f6'}; width: 32px; height: 32px; border-radius: 999px; border: 3px solid #fff; box-shadow: var(--shadow-lg); display: grid; place-items: center; color: #fff;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                                </div>
                            `;

                            const customIcon = L.divIcon({
                                html: markerHtml,
                                className: 'custom-div-icon',
                                iconSize: [32, 32],
                                iconAnchor: [16, 16]
                            });

                            L.marker([lat, lng], {icon: customIcon})
                                .addTo(map)
                                .bindPopup(`<strong>${vehicle.name}</strong><br>Status: ${vehicle.status}`);
                        });

                        // Add Charging Stations
                        const stations = [
                            { name: 'Abreeza Hub', lat: 7.0945, lng: 125.6122, rate: 18 },
                            { name: 'SM Lanang Premier', lat: 7.1022, lng: 125.6234, rate: 20 },
                            { name: 'Ecoland Terminal', lat: 7.0512, lng: 125.5945, rate: 12 }
                        ];

                        stations.forEach(station => {
                            const stationIcon = L.divIcon({
                                html: `
                                    <div style="background: #0f172a; width: 28px; height: 28px; border-radius: 8px; border: 2px solid #fff; box-shadow: var(--shadow-md); display: grid; place-items: center; color: #fff;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2.5"><path d="M15 7h1a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-1"/><path d="M6 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><line x1="11" y1="7" x2="13" y2="7"/><line x1="11" y1="17" x2="13" y2="17"/><path d="m10 11 2 2 2-2"/></svg>
                                    </div>
                                `,
                                className: 'custom-div-icon',
                                iconSize: [28, 28],
                                iconAnchor: [14, 14]
                            });

                            L.marker([station.lat, station.lng], {icon: stationIcon})
                                .addTo(map)
                                .bindPopup(`<strong>${station.name} Station</strong><br>₱${station.rate}/kWh`);
                        });

                        L.control.zoom({ position: 'bottomright' }).addTo(map);
                    });
                </script>
            </div>

            {{-- Activity / Alerts --}}
            <div style="display: grid; gap: 32px; align-content: start;">
                <div class="panel">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                        <h2 style="font-size: 1.1rem;">Operational Alerts</h2>
                        <span class="badge badge--danger">{{ count($alerts) }}</span>
                    </div>
                    <div style="display: grid; gap: 16px;">
                        @foreach ($alerts as $alert)
                            <article style="display: flex; gap: 16px; padding: 16px; background: var(--bg-soft); border-radius: 16px;">
                                <div style="width: 40px; height: 40px; background: {{ $alert['level'] === 'Critical' ? '#fee2e2' : '#eff6ff' }}; color: {{ $alert['level'] === 'Critical' ? '#ef4444' : '#3b82f6' }}; border-radius: 12px; display: grid; place-items: center; flex-shrink: 0;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
                                </div>
                                <div>
                                    <strong style="display: block; font-size: 0.94rem; margin-bottom: 4px;">{{ $alert['title'] }}</strong>
                                    <p style="font-size: 0.82rem; color: var(--muted); margin-bottom: 8px;">{{ $alert['copy'] }}</p>
                                    <a href="#" class="text-link" style="font-size: 0.76rem;">Resolve &rarr;</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                <div class="panel" style="background: #0f172a; color: #fff; border: none;">
                    <h2 style="font-size: 1.1rem; margin-bottom: 16px;">System Health</h2>
                    <div style="display: grid; gap: 16px;">
                        <div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.82rem; margin-bottom: 8px;">
                                <span style="opacity: 0.7;">Fleet Connectivity</span>
                                <strong style="color: #22c55e;">98.4%</strong>
                            </div>
                            <div style="height: 6px; background: rgba(255,255,255,0.1); border-radius: 999px;">
                                <div style="width: 98%; height: 100%; background: #22c55e; border-radius: inherit;"></div>
                            </div>
                        </div>
                        <div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.82rem; margin-bottom: 8px;">
                                <span style="opacity: 0.7;">Charging Network</span>
                                <strong style="color: #3b82f6;">Active</strong>
                            </div>
                            <div style="height: 6px; background: rgba(255,255,255,0.1); border-radius: 999px;">
                                <div style="width: 100%; height: 100%; background: #3b82f6; border-radius: inherit;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Fleet Status --}}
        <div class="panel">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="font-size: 1.25rem;">Live Fleet Status</h2>
                <a href="{{ route('fleet.index') }}" class="text-link">Full Inventory &rarr;</a>
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--line); text-align: left;">
                            <th style="padding: 16px; color: var(--muted); font-size: 0.82rem; text-transform: uppercase;">Vehicle</th>
                            <th style="padding: 16px; color: var(--muted); font-size: 0.82rem; text-transform: uppercase;">Status</th>
                            <th style="padding: 16px; color: var(--muted); font-size: 0.82rem; text-transform: uppercase;">Charge</th>
                            <th style="padding: 16px; color: var(--muted); font-size: 0.82rem; text-transform: uppercase;">Location</th>
                            <th style="padding: 16px; color: var(--muted); font-size: 0.82rem; text-transform: uppercase;">Health</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vehicles->take(6) as $vehicle)
                            <tr style="border-bottom: 1px solid var(--bg-soft);">
                                <td style="padding: 16px;">
                                    <strong>{{ $vehicle->name }}</strong>
                                    <span style="display: block; font-size: 0.76rem; color: var(--muted);">{{ $vehicle->brand }} {{ $vehicle->model }}</span>
                                </td>
                                <td style="padding: 16px;">
                                    <span class="status-pill {{ 'status-'.$vehicle->status }}">{{ $vehicle->status }}</span>
                                </td>
                                <td style="padding: 16px;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <strong>{{ $vehicle->battery_soc }}%</strong>
                                        <div style="width: 48px; height: 6px; background: var(--bg-soft); border-radius: 999px;">
                                            <div style="width: {{ $vehicle->battery_soc }}%; height: 100%; background: {{ $vehicle->battery_soc < 20 ? '#ef4444' : '#22c55e' }}; border-radius: inherit;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 16px; font-size: 0.88rem;">{{ $vehicle->location_zone }}</td>
                                <td style="padding: 16px;">
                                    <span class="badge" style="background: #f0fdf4; color: #166534;">{{ $vehicle->battery_health }}%</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
