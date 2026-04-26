@extends('layouts.app')

@section('title', 'My Profile | ECROS')

@section('content')
    <div style="max-width: 800px; margin: 0 auto; display: grid; gap: 32px;">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Account Settings</span>
                <h2>Personal Profile</h2>
                <p class="lead">Manage your account information and preferences.</p>
            </div>
        </div>

        <div class="panel" style="padding: 40px;">
            <div style="display: flex; gap: 32px; align-items: flex-start; margin-bottom: 40px; padding-bottom: 40px; border-bottom: 1px solid var(--line);">
                <div style="width: 100px; height: 100px; background: var(--primary-soft); color: var(--primary); border-radius: 32px; display: grid; place-items: center; font-size: 2.5rem; font-weight: 800;">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div style="flex: 1;">
                    <h3 style="font-size: 1.5rem; margin-bottom: 4px;">{{ auth()->user()->name }}</h3>
                    <p style="color: var(--muted); margin-bottom: 16px;">{{ auth()->user()->email }}</p>
                    <div style="display: flex; gap: 8px;">
                        <span class="badge" style="background: #f0fdf4; color: #166534; padding: 6px 12px; border-radius: 999px; font-size: 0.75rem; font-weight: 700;">
                            {{ auth()->user()->license_verified ? 'Driver License Verified' : 'License Pending Verification' }}
                        </span>
                        <span class="badge" style="background: var(--bg-soft); color: var(--text); padding: 6px 12px; border-radius: 999px; font-size: 0.75rem; font-weight: 700;">
                            Customer Since {{ auth()->user()->created_at->format('M Y') }}
                        </span>
                    </div>
                </div>
                <button class="btn btn-secondary">Edit Photo</button>
            </div>

            <form style="display: grid; gap: 24px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                    <label class="modern-field" style="background: var(--bg-soft); border: 1px solid var(--line); border-radius: 16px; padding: 12px 20px;">
                        <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: var(--muted); display: block; margin-bottom: 4px;">Full Name</span>
                        <input type="text" value="{{ auth()->user()->name }}" style="width: 100%; border: none; background: transparent; font-size: 1rem; font-weight: 600; color: var(--text); outline: none;">
                    </label>
                    <label class="modern-field" style="background: var(--bg-soft); border: 1px solid var(--line); border-radius: 16px; padding: 12px 20px;">
                        <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: var(--muted); display: block; margin-bottom: 4px;">Email Address</span>
                        <input type="email" value="{{ auth()->user()->email }}" style="width: 100%; border: none; background: transparent; font-size: 1rem; font-weight: 600; color: var(--text); outline: none;">
                    </label>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                    <label class="modern-field" style="background: var(--bg-soft); border: 1px solid var(--line); border-radius: 16px; padding: 12px 20px;">
                        <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: var(--muted); display: block; margin-bottom: 4px;">Phone Number</span>
                        <input type="text" value="{{ auth()->user()->phone ?? '+63 912 345 6789' }}" style="width: 100%; border: none; background: transparent; font-size: 1rem; font-weight: 600; color: var(--text); outline: none;">
                    </label>
                    <label class="modern-field" style="background: var(--bg-soft); border: 1px solid var(--line); border-radius: 16px; padding: 12px 20px;">
                        <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: var(--muted); display: block; margin-bottom: 4px;">Preferred Zone</span>
                        <select style="width: 100%; border: none; background: transparent; font-size: 1rem; font-weight: 600; color: var(--text); outline: none; appearance: none;">
                            <option>Davao City - Central</option>
                            <option>Davao City - North</option>
                            <option>Davao City - South</option>
                        </select>
                    </label>
                </div>

                <div style="margin-top: 16px; display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" class="btn btn-secondary">Discard Changes</button>
                    <button type="button" class="btn btn-primary">Save Profile</button>
                </div>
            </form>
        </div>

        <div class="panel" style="padding: 32px; background: #fee2e2; border: 1px solid #fecaca;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="color: #991b1b; font-size: 1.1rem; margin-bottom: 4px;">Danger Zone</h3>
                    <p style="color: #b91c1c; font-size: 0.88rem;">Once you delete your account, there is no going back. Please be certain.</p>
                </div>
                <button class="btn" style="background: #ef4444; color: #fff; border: none; padding: 12px 24px; border-radius: 12px; font-weight: 700; cursor: pointer;">Delete Account</button>
            </div>
        </div>
    </div>
@endsection
