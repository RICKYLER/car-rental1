@extends('layouts.app')

@section('title', 'Bookings | ECROS')

@section('content')
    <div style="display: grid; gap: 32px;">
        <header style="display: flex; justify-content: space-between; align-items: flex-end;">
            <div>
                <span class="eyebrow" style="margin-bottom: 12px;">{{ $viewer->isAdmin() ? 'Fleet Reservations' : 'Trip History' }}</span>
                <h1 style="margin: 0;">{{ $viewer->isAdmin() ? 'Operational Oversight' : 'Your Journeys' }}</h1>
                <p style="color: var(--muted); font-size: 1.1rem; margin-top: 8px;">
                    {{ $viewer->isAdmin() ? 'Full visibility into all customer bookings.' : 'Manage your upcoming and past electric trips.' }}
                </p>
            </div>
            @if ($viewer->isCustomer())
                <a href="{{ route('bookings.create') }}" class="btn btn-primary">New Booking</a>
            @endif
        </header>

        <section class="metric-grid">
            <div class="metric-card">
                <div class="metric-card__icon" style="background: var(--primary-soft); color: var(--primary);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
                </div>
                <div class="metric-card__body">
                    <span>Total</span>
                    <strong>{{ $summary['total'] }}</strong>
                </div>
            </div>
            <div class="metric-card">
                <div class="metric-card__icon" style="background: #f0fdf4; color: #166534;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="metric-card__body">
                    <span>Confirmed</span>
                    <strong>{{ $summary['confirmed'] }}</strong>
                </div>
            </div>
            <div class="metric-card">
                <div class="metric-card__icon" style="background: #eff6ff; color: #1d4ed8;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="metric-card__body">
                    <span>Active</span>
                    <strong>{{ $summary['active'] }}</strong>
                </div>
            </div>
            <div class="metric-card">
                <div class="metric-card__icon" style="background: #fdf2f8; color: #9d174d;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <div class="metric-card__body">
                    <span>{{ $viewer->isAdmin() ? 'Revenue' : 'Total Spend' }}</span>
                    <strong>₱{{ number_format($summary['value'], 0) }}</strong>
                </div>
            </div>
        </section>

        <div style="display: grid; gap: 20px;">
            @forelse ($bookings as $booking)
                <article class="panel" style="display: flex; justify-content: space-between; align-items: center; padding: 24px;">
                    <div style="display: flex; gap: 24px; align-items: center;">
                        <div style="width: 56px; height: 56px; background: var(--bg-soft); border-radius: 16px; display: grid; place-items: center; color: var(--primary);">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 4px;">
                                <strong style="font-size: 1.1rem;">{{ $booking->reference }}</strong>
                                <span class="status-pill {{ 'status-'.$booking->status }}">{{ $booking->status }}</span>
                            </div>
                            <p style="color: var(--muted); margin: 0;">
                                {{ $viewer->isAdmin() ? 'Reserved by '.$booking->user->name : $booking->vehicle->name }} 
                                &middot; {{ $booking->start_at->format('M d, Y') }}
                            </p>
                        </div>
                    </div>
                    
                    <div style="text-align: right;">
                        <strong style="display: block; font-size: 1.25rem; margin-bottom: 4px;">₱{{ number_format($booking->total_cost, 0) }}</strong>
                        <span style="color: var(--muted); font-size: 0.88rem;">{{ $booking->estimated_distance_km }} km journey</span>
                    </div>
                </article>
            @empty
                <div class="panel" style="padding: 64px; text-align: center;">
                    <h2 style="color: var(--muted);">No bookings recorded yet.</h2>
                </div>
            @endforelse
        </div>
    </div>
@endsection
