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
                            <div>
                                <h3>Role-aware navigation</h3>
                                <p>Customers land on a trip dashboard. Admins go directly to fleet operations.</p>
                            </div>
                        </article>
                        <article class="list-card">
                            <div>
                                <h3>Password reset support</h3>
                                <p>Reset links use Laravel's built-in broker and current mail log driver.</p>
                            </div>
                        </article>
                        <article class="list-card">
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
