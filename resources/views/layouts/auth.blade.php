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

        <div class="auth-split-layout">
            <section class="auth-split-form-side">
                <a href="{{ route('home') }}" class="brand auth-header-modern">
                    <span class="brand__mark">
                        <span class="brand__spark"></span>
                    </span>
                    <span class="brand__copy">
                        <strong>ECROS</strong>
                    </span>
                </a>

                <div class="modern-form-container">
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
                </div>
            </section>

            <section class="auth-split-visual-side">
                <img src="{{ asset('images/login-car.png') }}" alt="ECROS Mobility" class="auth-visual-asset">
                
                <div class="page-glow page-glow--one"></div>
                <div class="page-glow page-glow--two"></div>
            </section>
        </div>
    </body>
</html>
