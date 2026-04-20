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
    </head>
    <body>
        @php
            $authUser = auth()->user();

            if ($authUser?->isAdmin()) {
                $navLinks = [
                    ['label' => 'Home', 'route' => 'home', 'active' => request()->routeIs('home')],
                    ['label' => 'Fleet', 'route' => 'fleet.index', 'active' => request()->routeIs('fleet.*')],
                    ['label' => 'Trips', 'route' => 'bookings.index', 'active' => request()->routeIs('bookings.*')],
                    ['label' => 'Admin', 'route' => 'admin.dashboard', 'active' => request()->routeIs('admin.*')],
                ];
            } elseif ($authUser) {
                $navLinks = [
                    ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => request()->routeIs('dashboard')],
                    ['label' => 'Fleet', 'route' => 'fleet.index', 'active' => request()->routeIs('fleet.*')],
                    ['label' => 'My Trips', 'route' => 'bookings.index', 'active' => request()->routeIs('bookings.*')],
                ];
            } else {
                $navLinks = [
                    ['label' => 'Home', 'route' => 'home', 'active' => request()->routeIs('home')],
                    ['label' => 'Fleet', 'route' => 'fleet.index', 'active' => request()->routeIs('fleet.*')],
                ];
            }
        @endphp

        <div class="page-glow page-glow--one"></div>
        <div class="page-glow page-glow--two"></div>

        <div class="site-shell">
            <header class="topbar panel panel--glass">
                <a href="{{ route('home') }}" class="brand">
                    <span class="brand__mark">
                        <span class="brand__spark"></span>
                    </span>
                    <span class="brand__copy">
                        <strong>ECROS</strong>
                        <small>Electric mobility, made calmer</small>
                    </span>
                </a>

                <div class="topbar__actions">
                    <nav class="nav-links">
                        @foreach ($navLinks as $link)
                            <a
                                href="{{ route($link['route']) }}"
                                @class(['is-active' => $link['active']])
                            >
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </nav>

                    <div class="topbar__user-actions">
                        @guest
                            <a class="btn btn-ghost" href="{{ route('login') }}">Log in</a>
                            <a class="btn btn-primary btn-primary--compact" href="{{ route('register') }}">Create account</a>
                        @else
                            <div class="account-pill">
                                <div class="account-pill__meta">
                                    <strong>{{ $authUser->name }}</strong>
                                    <span>{{ $authUser->email }}</span>
                                </div>
                                <span class="account-pill__role">{{ ucfirst($authUser->role) }}</span>
                            </div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="btn btn-secondary btn-secondary--compact" type="submit">Log out</button>
                            </form>
                        @endguest
                    </div>
                </div>
            </header>

            @if (session('status'))
                <div class="flash flash--success">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="flash flash--error">
                    <strong>Submission issue.</strong>
                    <ul class="error-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <main class="page-content">
                @yield('content')
            </main>

            <footer class="site-footer">
                <span>Customer booking, live battery data, and charging-aware routing in one flow.</span>
                <span>Laravel demo experience for the ECROS platform.</span>
            </footer>

            <nav class="mobile-nav panel panel--glass" aria-label="Primary">
                @foreach ($navLinks as $link)
                    <a
                        href="{{ route($link['route']) }}"
                        @class(['is-active' => $link['active']])
                    >
                        <span>{{ $link['label'] }}</span>
                    </a>
                @endforeach

                @guest
                    <a href="{{ route('login') }}" @class(['is-active' => request()->routeIs('login')])>
                        <span>Login</span>
                    </a>
                    <a href="{{ route('register') }}" @class(['is-active' => request()->routeIs('register')])>
                        <span>Register</span>
                    </a>
                @else
                    <form method="POST" action="{{ route('logout') }}" class="mobile-nav__form">
                        @csrf
                        <button class="mobile-nav__button" type="submit">Logout</button>
                    </form>
                @endguest
            </nav>
        </div>
    </body>
</html>
