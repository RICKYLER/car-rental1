@extends('layouts.auth')

@section('title', 'Register | ECROS')

@section('content')
    <div class="auth-stack">
        <div>
            <span class="eyebrow">Create account</span>
            <h2>Register as a customer renter</h2>
            <p class="lead">
                Create a customer login to save your preferred pickup zone, track bookings, and reserve EVs securely.
            </p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="form-grid">
            @csrf

            <label>
                <span>Full name</span>
                <input type="text" name="name" value="{{ old('name') }}" autocomplete="name" required>
            </label>

            <label>
                <span>Email address</span>
                <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
            </label>

            <label>
                <span>Phone number</span>
                <input type="text" name="phone" value="{{ old('phone') }}" autocomplete="tel" required>
            </label>

            <label>
                <span>Preferred pickup zone</span>
                <input type="text" name="preferred_zone" value="{{ old('preferred_zone') }}" placeholder="Optional preferred zone">
            </label>

            <label>
                <span>Password</span>
                <input type="password" name="password" autocomplete="new-password" required>
            </label>

            <label>
                <span>Confirm password</span>
                <input type="password" name="password_confirmation" autocomplete="new-password" required>
            </label>

            <div class="form-grid__full">
                <button class="btn btn-primary" type="submit">Create customer account</button>
            </div>
        </form>

        <div class="auth-switch">
            <span>Already have an account?</span>
            <a class="text-link" href="{{ route('login') }}">Log in instead</a>
        </div>
    </div>
@endsection
