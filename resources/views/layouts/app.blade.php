<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'ECROS')</title>
        <meta name="description" content="Electric Car Rental Operations System mock platform built with Laravel.">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/ecros.css') }}">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    </head>
    <body>
        @php
            $authUser = auth()->user();

            if ($authUser?->isAdmin()) {
                $navGroups = [
                    'Overview' => [
                        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'layout-grid', 'active' => request()->routeIs('admin.dashboard')],
                        ['label' => 'Live Map', 'route' => 'admin.live-map', 'icon' => 'map', 'active' => request()->routeIs('admin.live-map')],
                        ['label' => 'Bookings', 'route' => 'bookings.index', 'icon' => 'calendar', 'active' => request()->routeIs('bookings.*'), 'badge' => 12],
                    ],
                    'Fleet' => [
                        ['label' => 'All Vehicles', 'route' => 'fleet.index', 'icon' => 'car', 'active' => request()->routeIs('fleet.index')],
                        ['label' => 'Maintenance', 'route' => 'fleet.index', 'icon' => 'wrench', 'active' => false, 'badge' => 2],
                        ['label' => 'Charging Status', 'route' => 'fleet.index', 'icon' => 'battery-charging', 'active' => false],
                    ],
                    'Business' => [
                        ['label' => 'Earnings Report', 'route' => 'admin.earnings', 'icon' => 'bar-chart-3', 'active' => request()->routeIs('admin.earnings')],
                        ['label' => 'Customers', 'route' => 'admin.customers', 'icon' => 'users', 'active' => request()->routeIs('admin.customers')],
                    ]
                ];
            } else {
                $navGroups = [
                    'Adventure' => [
                        ['label' => 'Nearby Vehicles', 'route' => 'dashboard', 'icon' => 'map', 'active' => request()->routeIs('dashboard')],
                        ['label' => 'Fleet Explorer', 'route' => 'fleet.index', 'icon' => 'search', 'active' => request()->routeIs('fleet.*')],
                    ],
                    'My ECROS' => [
                        ['label' => 'My Trips', 'route' => 'bookings.index', 'icon' => 'calendar', 'active' => request()->routeIs('bookings.*')],
                        ['label' => 'Profile', 'route' => 'dashboard', 'icon' => 'user', 'active' => false],
                    ],
                    'Support' => [
                        ['label' => 'Help Center', 'route' => 'home', 'icon' => 'help-circle', 'active' => false],
                    ]
                ];
            }
        @endphp

        <div class="site-shell">
            <aside class="sidebar">
                <a href="{{ route('home') }}" class="brand">
                    <span class="brand__mark">
                        <span class="brand__spark"></span>
                    </span>
                    <span class="brand__copy">
                        <strong>ECROS</strong>
                    </span>
                </a>

                <nav class="nav-links">
                    @foreach ($navGroups as $group => $links)
                        <div class="nav-section-title">{{ $group }}</div>
                        @foreach ($links as $link)
                            <a href="{{ route($link['route']) }}" @class(['is-active' => $link['active']])>
                                <span class="nav-link__icon">
                                    @if($link['icon'] === 'layout-grid')
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                                    @elseif($link['icon'] === 'map')
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/><line x1="9" y1="3" x2="9" y2="18"/><line x1="15" y1="6" x2="15" y2="21"/></svg>
                                    @elseif($link['icon'] === 'calendar')
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    @elseif($link['icon'] === 'car')
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                                    @elseif($link['icon'] === 'wrench')
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                                    @elseif($link['icon'] === 'battery-charging')
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 7h1a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-1"/><path d="M6 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><line x1="11" y1="7" x2="13" y2="7"/><line x1="11" y1="17" x2="13" y2="17"/><path d="m10 11 2 2 2-2"/></svg>
                                    @elseif($link['icon'] === 'bar-chart-3')
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
                                    @elseif($link['icon'] === 'users')
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    @elseif($link['icon'] === 'search')
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                                    @elseif($link['icon'] === 'user')
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    @elseif($link['icon'] === 'help-circle')
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                    @elseif($link['icon'] === 'alert-triangle')
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
                                    @else
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/></svg>
                                    @endif
                                </span>
                                <span>{{ $link['label'] }}</span>
                                @if (isset($link['badge']))
                                    <span class="badge badge--primary" style="margin-left: auto;">{{ $link['badge'] }}</span>
                                @endif
                            </a>
                        @endforeach
                    @endforeach
                </nav>

                <div class="sidebar-footer" style="margin-top: auto; padding-top: 24px; border-top: 1px solid var(--line); display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                    @auth
                        <div class="account-pill" style="border: none; background: transparent; padding: 0;">
                            <div class="account-pill__meta">
                                <strong style="font-size: 0.9rem;">{{ $authUser->name }}</strong>
                                <span style="font-size: 0.76rem; color: var(--muted);">{{ ucfirst($authUser->role) }}</span>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn-icon" style="color: var(--danger);">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                            </button>
                        </form>
                    @endauth
                </div>
            </aside>

            <div class="main-content-wrap">
                <header class="main-header">
                    <div class="search-bar">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        <input type="text" placeholder="Search vehicles, bookings, or customers...">
                    </div>

                    <div class="header-actions" style="display: flex; align-items: center; gap: 20px;">
                        <button class="btn-icon notification-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                        </button>
                        <button class="btn btn-primary btn-primary--compact">+ Add Vehicle</button>
                    </div>
                </header>

                <main class="page-content">
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
