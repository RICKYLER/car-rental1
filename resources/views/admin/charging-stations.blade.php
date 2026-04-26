@extends('layouts.app')

@section('title', 'Charging Stations | ECROS')

@section('content')
    <div style="display: grid; gap: 32px;">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Infrastructure</span>
                <h2>Charging Network</h2>
                <p class="lead">Manage charging station locations, rates, and availability.</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <button class="btn btn-primary">+ Add Station</button>
            </div>
        </div>

        <div class="metric-grid">
            <article class="metric-card">
                <div class="metric-card__icon" style="background: #eff6ff; color: #3b82f6;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 7h1a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-1"/><path d="M6 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><line x1="11" y1="7" x2="13" y2="7"/><line x1="11" y1="17" x2="13" y2="17"/><path d="m10 11 2 2 2-2"/></svg>
                </div>
                <div class="metric-card__body">
                    <span>Total Stations</span>
                    <strong>12</strong>
                </div>
            </article>

            <article class="metric-card">
                <div class="metric-card__icon" style="background: #f0fdf4; color: #22c55e;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="metric-card__body">
                    <span>Active Now</span>
                    <strong>10</strong>
                </div>
            </article>

            <article class="metric-card">
                <div class="metric-card__icon" style="background: #fff7ed; color: #f97316;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="m17 5-5-3-5 3"/><path d="m17 19-5 3-5-3"/></svg>
                </div>
                <div class="metric-card__body">
                    <span>Avg. Rate</span>
                    <strong>₱15/kWh</strong>
                </div>
            </article>
        </div>

        <div class="panel">
            <div style="padding: 24px; border-bottom: 1px solid var(--line);">
                <h3 style="font-size: 1.1rem;">Station Directory & Rates</h3>
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--line); text-align: left;">
                            <th style="padding: 16px; color: var(--muted); font-size: 0.82rem; text-transform: uppercase;">Station Name</th>
                            <th style="padding: 16px; color: var(--muted); font-size: 0.82rem; text-transform: uppercase;">Location</th>
                            <th style="padding: 16px; color: var(--muted); font-size: 0.82rem; text-transform: uppercase;">Type</th>
                            <th style="padding: 16px; color: var(--muted); font-size: 0.82rem; text-transform: uppercase;">Rate (₱/kWh)</th>
                            <th style="padding: 16px; color: var(--muted); font-size: 0.82rem; text-transform: uppercase;">Status</th>
                            <th style="padding: 16px; color: var(--muted); font-size: 0.82rem; text-transform: uppercase;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $stations = [
                                ['name' => 'Abreeza Hub', 'location' => 'Bajada, Davao City', 'type' => 'Level 3 Fast', 'rate' => 18, 'status' => 'online'],
                                ['name' => 'SM Lanang Premier', 'location' => 'Lanang, Davao City', 'type' => 'Level 3 Fast', 'rate' => 20, 'status' => 'online'],
                                ['name' => 'Ecoland Terminal', 'location' => 'Ecoland, Davao City', 'type' => 'Level 2', 'rate' => 12, 'status' => 'online'],
                                ['name' => 'Matina Town Square', 'location' => 'Matina, Davao City', 'type' => 'Level 2', 'rate' => 12, 'status' => 'offline'],
                                ['name' => 'Toril Central', 'location' => 'Toril, Davao City', 'type' => 'Level 2', 'rate' => 10, 'status' => 'online'],
                            ];
                        @endphp
                        @foreach ($stations as $station)
                            <tr style="border-bottom: 1px solid var(--bg-soft);">
                                <td style="padding: 16px;">
                                    <strong>{{ $station['name'] }}</strong>
                                </td>
                                <td style="padding: 16px; font-size: 0.88rem;">{{ $station['location'] }}</td>
                                <td style="padding: 16px; font-size: 0.88rem;">
                                    <span class="badge">{{ $station['type'] }}</span>
                                </td>
                                <td style="padding: 16px;">
                                    <strong>₱{{ $station['rate'] }}</strong>
                                </td>
                                <td style="padding: 16px;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span style="width: 8px; height: 8px; background: {{ $station['status'] === 'online' ? '#22c55e' : '#ef4444' }}; border-radius: 999px;"></span>
                                        <span style="font-size: 0.88rem; font-weight: 600; text-transform: capitalize;">{{ $station['status'] }}</span>
                                    </div>
                                </td>
                                <td style="padding: 16px;">
                                    <button class="btn btn-secondary btn-primary--compact">Edit Rate</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
