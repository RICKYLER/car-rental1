@extends('layouts.app')

@section('title', 'Customers | ECROS')

@section('content')
    <div style="display: grid; gap: 32px;">
        <header style="display: flex; justify-content: space-between; align-items: flex-end;">
            <div>
                <span class="eyebrow">Relationship Management</span>
                <h1>Customer Directory</h1>
                <p style="color: var(--muted);">Manage your user base and view their engagement metrics.</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <button class="btn btn-primary">+ Invite Customer</button>
            </div>
        </header>

        <div class="panel" style="padding: 0; overflow: hidden;">
            <div style="padding: 24px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center;">
                <div class="search-bar" style="width: 320px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    <input type="text" placeholder="Search by name or email..." style="background: var(--bg-soft); border-radius: 12px; padding-left: 48px;">
                </div>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <span style="font-size: 0.88rem; color: var(--muted);">Sort by:</span>
                    <select style="border: 1px solid var(--line); border-radius: 8px; padding: 4px 12px; font-size: 0.88rem;">
                        <option>Newest First</option>
                        <option>Most Active</option>
                    </select>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--line); text-align: left;">
                            <th style="padding: 20px; font-size: 0.82rem; color: var(--muted); text-transform: uppercase;">Customer</th>
                            <th style="padding: 20px; font-size: 0.82rem; color: var(--muted); text-transform: uppercase;">Status</th>
                            <th style="padding: 20px; font-size: 0.82rem; color: var(--muted); text-transform: uppercase;">Total Trips</th>
                            <th style="padding: 20px; font-size: 0.82rem; color: var(--muted); text-transform: uppercase;">Joined Date</th>
                            <th style="padding: 20px; font-size: 0.82rem; color: var(--muted); text-transform: uppercase; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr style="border-bottom: 1px solid var(--line); transition: var(--transition);" onmouseover="this.style.background='var(--bg-soft)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 20px;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 40px; height: 40px; border-radius: 12px; background: #f1f5f9; display: grid; place-items: center; font-weight: 700; color: var(--primary);">
                                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 600;">{{ $customer->name }}</div>
                                            <div style="font-size: 0.76rem; color: var(--muted);">{{ $customer->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 20px;">
                                    <span class="badge" style="background: #dcfce7; color: #166534; border: none;">Active</span>
                                </td>
                                <td style="padding: 20px; font-weight: 600;">{{ $customer->bookings_count }} Trips</td>
                                <td style="padding: 20px; color: var(--muted);">{{ $customer->created_at->format('M d, Y') }}</td>
                                <td style="padding: 20px; text-align: right;">
                                    <button class="btn-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding: 24px; border-top: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 0.88rem; color: var(--muted);">Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} customers</span>
                <div style="display: flex; gap: 8px;">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
