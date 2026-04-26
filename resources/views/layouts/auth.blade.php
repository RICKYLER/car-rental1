<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'ECROS Access')</title>
        <meta name="description" content="Access ECROS customer and admin experiences.">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/ecros.css') }}">
    </head>
    <body>
        <div class="page-glow page-glow--one"></div>
        <div class="page-glow page-glow--two"></div>

        <div class="auth-shell">
            <a href="{{ route('home') }}" class="brand brand--auth">
                <span class="brand__mark">
                    <span class="brand__spark"></span>
                </span>
                <span class="brand__copy">
                    <strong>ECROS</strong>
                    <small>Secure access for electric mobility operations</small>
                </span>
            </a>

            <main class="auth-layout">
                <section class="panel panel--dark auth-side">
                    <span class="eyebrow eyebrow--dark">Professional access</span>
                    <h1>One sign-in flow for customers and operators.</h1>
                    <p class="lead">
                        The ECROS access experience keeps fleet operations secure while making customer booking
                        flow feel credible, calm, and presentation-ready for demo use.
                    </p>

                    <div class="auth-feature-list">
                        <article class="list-card">
                            <div class="list-card__icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            </div>
                            <div>
                                <h3>Role-aware navigation</h3>
                                <p>Customers land on a trip dashboard. Admins go directly to fleet operations.</p>
                            </div>
                        </article>
                        <article class="list-card">
                            <div class="list-card__icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </div>
                            <div>
                                <h3>Password reset support</h3>
                                <p>Reset links use Laravel's built-in broker and current mail log driver.</p>
                            </div>
                        </article>
                        <article class="list-card">
                            <div class="list-card__icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                            </div>
                            <div>
                                <h3>Demo-ready accounts</h3>
                                <p>Evaluators can access both roles without extra setup or database editing.</p>
                            </div>
                        </article>
                    </div>
                </section>

                <section class="panel auth-main">
                    @if (session('status'))
                        <div class="flash flash--success flash--inline">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="flash flash--error flash--inline">
                            <strong>Submission issue.</strong>
                            <ul class="error-list">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </section>
            </main>
        </div>
    </body>
</html>
