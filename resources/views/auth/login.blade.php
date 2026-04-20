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
                <article class="auth-demo-card">
                    <span class="eyebrow">{{ $account['label'] }}</span>
                    <h3>{{ $account['email'] }}</h3>
                    <p>{{ $account['description'] }}</p>
                    <div class="auth-demo-card__credentials">
                        <span>Email: {{ $account['email'] }}</span>
                        <span>Password: {{ $account['password'] }}</span>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
@endsection
