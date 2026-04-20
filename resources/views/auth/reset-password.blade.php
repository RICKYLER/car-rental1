@extends('layouts.auth')

@section('title', 'Reset Password | ECROS')

@section('content')
    <div class="auth-stack">
        <div>
            <span class="eyebrow">Choose a new password</span>
            <h2>Complete your password reset</h2>
            <p class="lead">
                Set a new password for your ECROS account and return to the login flow.
            </p>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="form-grid form-grid--single">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <label>
                <span>Email address</span>
                <input type="email" name="email" value="{{ old('email', $email) }}" autocomplete="email" required>
            </label>

            <label>
                <span>New password</span>
                <input type="password" name="password" autocomplete="new-password" required>
            </label>

            <label>
                <span>Confirm new password</span>
                <input type="password" name="password_confirmation" autocomplete="new-password" required>
            </label>

            <button class="btn btn-primary" type="submit">Reset password</button>
        </form>
    </div>
@endsection
