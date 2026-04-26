@extends('layouts.app')

@section('title', 'Maintenance | ECROS')

@section('content')
    <div style="display: grid; gap: 32px;">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Fleet Health</span>
                <h2>Maintenance Management</h2>
                <p class="lead">Monitor and schedule service for vehicles requiring attention.</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <button class="btn btn-secondary">Download Log</button>
                <button class="btn btn-primary">Schedule Service</button>
            </div>
        </div>

        <div class="metric-grid">
            <article class="metric-card">
                <div class="metric-card__icon" style="background: #fef2f2; color: #ef4444;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
                </div>
                <div class="metric-card__body">
                    <span>Critical Alerts</span>
                    <strong>3</strong>
                    <div class="metric-card__trend trend--down">Action Needed</div>
                </div>
            </article>

            <article class="metric-card">
                <div class="metric-card__icon" style="background: #fff7ed; color: #f97316;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="metric-card__body">
                    <span>Expiring Soon</span>
                    <strong>5</strong>
                    <div class="metric-card__trend">Next 7 days</div>
                </div>
            </article>

            <article class="metric-card">
                <div class="metric-card__icon" style="background: #f0fdf4; color: #22c55e;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="metric-card__body">
                    <span>Completed</span>
                    <strong>12</strong>
                    <div class="metric-card__trend trend--up">This month</div>
                </div>
            </article>
        </div>

        <div class="panel">
            <div style="padding: 24px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1.1rem;">Vehicles Requiring Service</h3>
                <div class="filter-actions">
                    <select class="badge" style="border: 1px solid var(--line); background: #fff; padding: 6px 12px;">
                        <option>All Issues</option>
                        <option>Critical</option>
                        <option>Warning</option>
                    </select>
                </div>
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--line); text-align: left;">
                            <th style="padding: 16px; color: var(--muted); font-size: 0.82rem; text-transform: uppercase;">Vehicle</th>
                            <th style="padding: 16px; color: var(--muted); font-size: 0.82rem; text-transform: uppercase;">Issue Type</th>
                            <th style="padding: 16px; color: var(--muted); font-size: 0.82rem; text-transform: uppercase;">Last Service</th>
                            <th style="padding: 16px; color: var(--muted); font-size: 0.82rem; text-transform: uppercase;">Next Service</th>
                            <th style="padding: 16px; color: var(--muted); font-size: 0.82rem; text-transform: uppercase;">Status</th>
                            <th style="padding: 16px; color: var(--muted); font-size: 0.82rem; text-transform: uppercase;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($maintenanceVehicles as $vehicle)
                            <tr style="border-bottom: 1px solid var(--bg-soft);">
                                <td style="padding: 16px;">
                                    <strong>{{ $vehicle->name }}</strong>
                                    <span style="display: block; font-size: 0.76rem; color: var(--muted);">{{ $vehicle->brand }} {{ $vehicle->model }}</span>
                                </td>
                                <td style="padding: 16px;">
                                    @if($vehicle->battery_health < 85)
                                        <div style="display: flex; align-items: center; gap: 8px; color: var(--danger);">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M23 12a11 11 0 1 1-22 0 11 11 0 0 1 22 0z"/><path d="m9 12 2 2 4-4"/></svg>
                                            <span style="font-size: 0.88rem; font-weight: 600;">Battery Health Low</span>
                                        </div>
                                    @else
                                        <div style="display: flex; align-items: center; gap: 8px; color: #f97316;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                            <span style="font-size: 0.88rem; font-weight: 600;">Routine Checkup</span>
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 16px; font-size: 0.88rem;">Mar 12, 2026</td>
                                <td style="padding: 16px; font-size: 0.88rem;">Apr 28, 2026</td>
                                <td style="padding: 16px;">
                                    <span class="badge" style="background: {{ $vehicle->battery_health < 85 ? '#fee2e2' : '#fff7ed' }}; color: {{ $vehicle->battery_health < 85 ? '#ef4444' : '#f97316' }};">
                                        {{ $vehicle->battery_health < 85 ? 'Critical' : 'Pending' }}
                                    </span>
                                </td>
                                <td style="padding: 16px;">
                                    <button class="btn btn-secondary btn-primary--compact">Manage</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
