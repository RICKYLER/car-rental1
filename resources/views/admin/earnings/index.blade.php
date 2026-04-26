@extends('layouts.app')

@section('title', 'Earnings Report | ECROS')

@section('content')
    <div style="display: grid; gap: 32px;">
        <header style="display: flex; justify-content: space-between; align-items: flex-end;">
            <div>
                <span class="eyebrow">Business Intelligence</span>
                <h1>Earnings Report</h1>
                <p style="color: var(--muted);">Track your fleet's financial performance and growth.</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <button class="btn btn-secondary">Download CSV</button>
                <button class="btn btn-primary">Print PDF</button>
            </div>
        </header>

        <div class="metric-grid">
            <article class="metric-card">
                <div class="metric-card__icon" style="background: #eff6ff; color: #3b82f6;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <div class="metric-card__body">
                    <span>Total Revenue</span>
                    <strong>PHP {{ number_format($totalEarnings, 2) }}</strong>
                    <div style="color: var(--success); font-size: 0.82rem; font-weight: 700;">+{{ $monthlyGrowth }}% from last month</div>
                </div>
            </article>

            <article class="metric-card">
                <div class="metric-card__icon" style="background: #f0fdf4; color: #22c55e;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="metric-card__body">
                    <span>Active Subscriptions</span>
                    <strong>128 Users</strong>
                    <div style="color: var(--success); font-size: 0.82rem; font-weight: 700;">+4.2% growth</div>
                </div>
            </article>

            <article class="metric-card">
                <div class="metric-card__icon" style="background: #fdf4ff; color: #d946ef;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="m17 5-5-3-5 3"/><path d="m17 19-5 3-5-3"/><rect width="20" height="8" x="2" y="8" rx="2"/></svg>
                </div>
                <div class="metric-card__body">
                    <span>Avg. Trip Value</span>
                    <strong>PHP 1,240.00</strong>
                    <div style="color: var(--muted); font-size: 0.82rem;">Across {{ $recentTransactions->count() }} bookings</div>
                </div>
            </article>
        </div>

        <div class="panel">
            <h2 style="font-size: 1.25rem; margin-bottom: 24px;">Recent Completed Transactions</h2>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--line); text-align: left;">
                            <th style="padding: 16px; font-size: 0.82rem; color: var(--muted); text-transform: uppercase;">Reference</th>
                            <th style="padding: 16px; font-size: 0.82rem; color: var(--muted); text-transform: uppercase;">Customer</th>
                            <th style="padding: 16px; font-size: 0.82rem; color: var(--muted); text-transform: uppercase;">Vehicle</th>
                            <th style="padding: 16px; font-size: 0.82rem; color: var(--muted); text-transform: uppercase;">Date</th>
                            <th style="padding: 16px; font-size: 0.82rem; color: var(--muted); text-transform: uppercase; text-align: right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentTransactions as $tx)
                            <tr style="border-bottom: 1px solid var(--line); transition: var(--transition);" onmouseover="this.style.background='var(--bg-soft)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 16px;">
                                    <code style="background: var(--bg-soft); padding: 4px 8px; border-radius: 6px;">#{{ substr($tx->id, 0, 8) }}</code>
                                </td>
                                <td style="padding: 16px;">
                                    <div style="font-weight: 600;">{{ $tx->user->name }}</div>
                                    <div style="font-size: 0.76rem; color: var(--muted);">{{ $tx->user->email }}</div>
                                </td>
                                <td style="padding: 16px;">{{ $tx->vehicle->name }}</td>
                                <td style="padding: 16px;">{{ $tx->start_at->format('M d, Y') }}</td>
                                <td style="padding: 16px; text-align: right; font-weight: 700;">PHP {{ number_format($tx->total_price, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding: 32px; text-align: center; color: var(--muted);">No transactions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
