@extends('layouts.auth')

@section('title', 'Login | ECROS')

@section('content')
    <div class="auth-stack">
        <div>
            <span class="eyebrow">Sign in</span>
            <h2>Log in to your ECROS account</h2>
            <p class="lead">
                Use your email and password to continue to the customer dashboard or fleet operations center.
            </p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="form-grid form-grid--single">
            @csrf

            <label>
                <span>Email address</span>
                <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
            </label>

            <label>
                <span>Password</span>
                <input type="password" name="password" autocomplete="current-password" required>
            </label>

            <div class="auth-row">
                <label class="checkbox-row">
                    <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                    <span>Keep me signed in on this device.</span>
                </label>

                <a class="text-link" href="{{ route('password.request') }}">Forgot password?</a>
            </div>

            <button class="btn btn-primary" type="submit">Log in</button>
        </form>

        <div class="auth-switch">
            <span>Need a customer account?</span>
            <a class="text-link" href="{{ route('register') }}">Create one now</a>
        </div>

        <div class="auth-demo-grid">
            @foreach ($demoAccounts as $account)
                <article class="auth-demo-card" onclick="fillDemo('{{ $account['email'] }}', '{{ $account['password'] }}')">
                    <div class="auth-demo-card__header">
                        <span class="eyebrow">{{ $account['label'] }}</span>
                        <div class="auth-demo-card__icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                        </div>
                    </div>
                    <h3>{{ $account['email'] }}</h3>
                    <p>{{ $account['description'] }}</p>
                    <div class="auth-demo-card__credentials">
                        <span>Email: <strong>{{ $account['email'] }}</strong></span>
                        <span>Password: <strong>{{ $account['password'] }}</strong></span>
                    </div>
                </article>
            @endforeach
        </div>
    </div>

    <script>
        function fillDemo(email, password) {
            document.querySelector('input[name="email"]').value = email;
            document.querySelector('input[name="password"]').value = password;
            
            // Pulse the button for visual feedback
            const button = document.querySelector('button[type="submit"]');
            button.classList.add('pulse-once');
            setTimeout(() => button.classList.remove('pulse-once'), 500);
        }
    </script>
@endsection
