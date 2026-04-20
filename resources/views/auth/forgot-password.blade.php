@extends('layouts.auth')

@section('title', 'Forgot Password | ECROS')

@section('content')
    <div class="auth-stack">
        <div>
            <span class="eyebrow">Reset password</span>
            <h2>Request a password reset link</h2>
            <p class="lead">
                Enter the email address linked to your account. In demo mode, the reset link is written to the application log.
            </p>
        </div>

        <form method="POST" action="{{ route('password.email') }}" class="form-grid form-grid--single">
            @csrf

            <label>
                <span>Email address</span>
                <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
            </label>

            <button class="btn btn-primary" type="submit">Send reset link</button>
        </form>

        <div class="auth-switch">
            <span>Remembered your password?</span>
            <a class="text-link" href="{{ route('login') }}">Back to login</a>
        </div>
    </div>
@endsection
