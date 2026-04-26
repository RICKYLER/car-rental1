@extends('layouts.auth')

@section('title', 'Login | ECROS')

@section('content')
    <div class="auth-header-modern">
        <h1>Welcome Back</h1>
        <p>Please enter your details to continue.</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="modern-input-group">
            <div class="modern-field">
                <label>
                    <span>Email Address</span>
                    <input type="email" name="email" value="{{ old('email', 'dane@ecros.test') }}" placeholder="name@company.com" autocomplete="email" required>
                </label>
            </div>

            <div class="modern-field">
                <label>
                    <span>Password</span>
                    <input type="password" name="password" value="password" placeholder="••••••••" autocomplete="current-password" required>
                </label>
            </div>
        </div>

        <div class="auth-row" style="margin-bottom: 24px;">
            <label class="checkbox-row">
                <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                <span style="font-size: 0.9rem; color: var(--muted);">Keep me signed in</span>
            </label>

            <a class="text-link" href="{{ route('password.request') }}" style="font-size: 0.9rem;">Forgot password?</a>
        </div>

        <button class="btn-modern" type="submit">Continue</button>
    </form>

    <div style="text-align: center; margin: 24px 0; color: var(--muted); font-size: 0.9rem; position: relative;">
        <span style="background: #fff; padding: 0 12px; position: relative; z-index: 1;">Or Continue With</span>
        <div style="position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: #e2e8f0; z-index: 0;"></div>
    </div>

    <div class="social-auth-grid">
        <button class="social-btn" title="Google">
            <svg width="20" height="20" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
        </button>
        <button class="social-btn" title="Apple">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.05 20.28c-.98.95-2.05 1.61-3.21 1.61-1.18 0-1.58-.72-2.95-.72-1.36 0-1.83.7-2.95.7-1.14 0-2.29-.72-3.32-1.68-2.11-1.96-3.72-5.54-3.72-8.66 0-3.09 1.62-4.74 3.16-4.74 1.14 0 1.9.65 2.76.65.72 0 1.5-.7 2.8-.7 1.09 0 2.05.51 2.66 1.34-2.58 1.55-2.16 5.16.52 6.45-.63 1.57-1.55 3.09-2.75 4.75zM12.03 5.41c-.02-1.89 1.55-3.5 3.33-3.41.22 1.95-1.55 3.65-3.33 3.41z"/></svg>
        </button>
        <button class="social-btn" title="Facebook">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
        </button>
    </div>

    <div class="form-footer-modern">
        <span>Need a customer account?</span>
        <a href="{{ route('register') }}">Create one now</a>
    </div>

    <div style="margin-top: 48px; border-top: 1px solid #e2e8f0; padding-top: 32px;">
        <span class="eyebrow" style="margin-bottom: 16px;">Demo Access</span>
        <div class="auth-demo-grid">
            @foreach ($demoAccounts as $account)
                <article class="auth-demo-card" style="padding: 16px;" onclick="fillDemo('{{ $account['email'] }}', '{{ $account['password'] }}')">
                    <div class="auth-demo-card__header" style="margin-bottom: 4px;">
                        <span style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: var(--primary);">{{ $account['label'] }}</span>
                    </div>
                    <strong style="font-size: 0.9rem; display: block; overflow: hidden; text-overflow: ellipsis;">{{ $account['email'] }}</strong>
                </article>
            @endforeach
        </div>
    </div>

    <script>
        function fillDemo(email, password) {
            document.querySelector('input[name="email"]').value = email;
            document.querySelector('input[name="password"]').value = password;
            
            // Pulse the button for visual feedback
            const button = document.querySelector('.btn-modern');
            button.classList.add('pulse-once');
            setTimeout(() => button.classList.remove('pulse-once'), 500);
        }
    </script>
@endsection
