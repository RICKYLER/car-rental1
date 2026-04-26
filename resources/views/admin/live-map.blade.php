@extends('layouts.app')

@section('title', 'Live Fleet Map | ECROS')

@section('content')
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 24px; height: calc(100vh - 140px);">
        {{-- Main Map Panel --}}
        <div class="panel" style="padding: 0; position: relative; overflow: hidden; border: none; box-shadow: var(--shadow-lg);">
            <div style="position: absolute; top: 24px; left: 24px; z-index: 10; display: flex; gap: 12px;">
                <div style="background: #fff; padding: 12px 20px; border-radius: 16px; box-shadow: var(--shadow-md); display: flex; align-items: center; gap: 12px;">
                    <span style="width: 10px; height: 10px; background: #22c55e; border-radius: 999px; animation: pulse 2s infinite;"></span>
                    <strong style="font-size: 0.94rem;">Live Feed Active</strong>
                </div>
                <div class="badge" style="background: rgba(255,255,255,0.9); backdrop-filter: blur(8px); padding: 12px 20px; border: 1px solid var(--line); box-shadow: var(--shadow-md);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    Search vehicles...
                </div>
            </div>

            <div id="live-fleet-map" style="height: 100%; background: #f1f5f9; position: relative; z-index: 1;"></div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const map = L.map('live-fleet-map', {
                        center: [7.0731, 125.6111],
                        zoom: 13,
                        zoomControl: false
                    });

                    // Google Maps Hybrid/Street Style Layer
                    L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                        maxZoom: 20,
                        subdomains:['mt0','mt1','mt2','mt3'],
                        attribution: '&copy; Google Maps'
                    }).addTo(map);

                    const vehicles = @json($vehicles);
                    
                    vehicles.forEach(vehicle => {
                        const markerHtml = `
                            <div style="background: ${vehicle.status === 'available' ? '#22c55e' : (vehicle.status === 'charging' ? '#3b82f6' : '#64748b')}; width: 44px; height: 44px; border-radius: 14px; border: 4px solid #fff; box-shadow: var(--shadow-lg); display: grid; place-items: center; color: #fff;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                                <div style="position: absolute; top: -10px; background: #0f172a; color: #fff; padding: 2px 6px; border-radius: 6px; font-size: 0.65rem; font-weight: 800;">${vehicle.battery_soc}%</div>
                            </div>
                        `;

                        const customIcon = L.divIcon({
                            html: markerHtml,
                            className: 'custom-div-icon',
                            iconSize: [44, 44],
                            iconAnchor: [22, 22]
                        });

                        // Randomized positions around Davao center for demo
                        const lat = 7.0731 + (Math.random() - 0.5) * 0.05;
                        const lng = 125.6111 + (Math.random() - 0.5) * 0.05;

                        L.marker([lat, lng], {icon: customIcon})
                            .addTo(map)
                            .bindPopup(`<strong>${vehicle.name}</strong><br>Status: ${vehicle.status}<br>Battery: ${vehicle.battery_soc}%`);
                    });

                    // Add Charging Stations
                    const stations = [
                        { name: 'Abreeza Hub', lat: 7.0945, lng: 125.6122, rate: 18 },
                        { name: 'SM Lanang Premier', lat: 7.1022, lng: 125.6234, rate: 20 },
                        { name: 'Ecoland Terminal', lat: 7.0512, lng: 125.5945, rate: 12 },
                        { name: 'Matina Town Square', lat: 7.0622, lng: 125.5999, rate: 12 }
                    ];

                    stations.forEach(station => {
                        const stationIcon = L.divIcon({
                            html: `
                                <div style="background: #0f172a; width: 36px; height: 36px; border-radius: 10px; border: 3px solid #fff; box-shadow: var(--shadow-md); display: grid; place-items: center; color: #fff;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 7h1a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-1"/><path d="M6 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><line x1="11" y1="7" x2="13" y2="7"/><line x1="11" y1="17" x2="13" y2="17"/><path d="m10 11 2 2 2-2"/></svg>
                                </div>
                            `,
                            className: 'custom-div-icon',
                            iconSize: [36, 36],
                            iconAnchor: [18, 18]
                        });

                        L.marker([station.lat, station.lng], {icon: stationIcon})
                            .addTo(map)
                            .bindPopup(`<strong>${station.name} Charging Station</strong><br>Rate: ₱${station.rate}/kWh`);
                    });

                    L.control.zoom({ position: 'bottomright' }).addTo(map);
                });
            </script>

            <div style="position: absolute; bottom: 24px; right: 24px; display: grid; gap: 8px;">
                <button style="width: 44px; height: 44px; border-radius: 12px; background: #fff; border: 1px solid var(--line); box-shadow: var(--shadow-md); display: grid; place-items: center; cursor: pointer;">+</button>
                <button style="width: 44px; height: 44px; border-radius: 12px; background: #fff; border: 1px solid var(--line); box-shadow: var(--shadow-md); display: grid; place-items: center; cursor: pointer;">-</button>
            </div>
        </div>

        {{-- Sidebar Vehicle List --}}
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <div class="panel" style="padding: 20px;">
                <h2 style="font-size: 1.1rem; margin-bottom: 16px;">Fleet Overview</h2>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div style="background: var(--bg-soft); padding: 12px; border-radius: 12px; text-align: center;">
                        <span style="display: block; font-size: 0.72rem; color: var(--muted); text-transform: uppercase; font-weight: 700;">Active</span>
                        <strong style="font-size: 1.25rem;">{{ $vehicles->count() }}</strong>
                    </div>
                    <div style="background: var(--bg-soft); padding: 12px; border-radius: 12px; text-align: center;">
                        <span style="display: block; font-size: 0.72rem; color: var(--muted); text-transform: uppercase; font-weight: 700;">Alerts</span>
                        <strong style="font-size: 1.25rem; color: var(--danger);">3</strong>
                    </div>
                </div>
            </div>

            <div class="panel" style="flex: 1; padding: 0; overflow: hidden; display: flex; flex-direction: column;">
                <div style="padding: 20px; border-bottom: 1px solid var(--line);">
                    <h3 style="font-size: 1rem;">Vehicle Status</h3>
                </div>
                <div style="flex: 1; overflow-y: auto; padding: 12px;">
                    @foreach ($vehicles as $vehicle)
                        <article style="display: flex; gap: 16px; padding: 12px; border-radius: 14px; transition: var(--transition); cursor: pointer;" 
                                 onmouseover="this.style.background='var(--bg-soft)'" onmouseout="this.style.background='transparent'">
                            <div style="width: 48px; height: 48px; background: #f1f5f9; border-radius: 12px; display: grid; place-items: center; flex-shrink: 0;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="{{ $vehicle->status === 'available' ? '#22c55e' : ($vehicle->status === 'charging' ? '#3b82f6' : '#64748b') }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <strong style="display: block; font-size: 0.94rem; margin-bottom: 2px;">{{ $vehicle->name }}</strong>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 0.76rem; color: var(--muted);">{{ $vehicle->location_zone }}</span>
                                    <span style="font-size: 0.82rem; font-weight: 700; color: {{ $vehicle->battery_soc < 20 ? 'var(--danger)' : 'var(--text)' }}">{{ $vehicle->battery_soc }}%</span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.5); opacity: 0.4; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
@endsection
