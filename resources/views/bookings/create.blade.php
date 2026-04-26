@extends('layouts.app')

@section('title', 'Create Booking')

@section('content')
    <section class="reservation-hero">
        <div>
            <span class="eyebrow">Booking engine</span>
            <h1 style="margin-top: 12px; margin-bottom: 16px;">Create a protected <br>customer reservation.</h1>
            <p class="lead" style="max-width: 600px;">
                Your account details are already on file, so this flow focuses on the trip itself, vehicle range, and pricing clarity.
            </p>
        </div>
        <div style="display: flex; flex-direction: column; gap: 12px; align-items: flex-end;">
            <a class="btn btn-secondary" href="{{ route('bookings.index') }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                View bookings
            </a>
            <span style="font-size: 0.8rem; color: var(--muted); font-weight: 600;">Secure 256-bit encryption active</span>
        </div>
    </section>

    <section class="section section--split booking-layout" style="gap: 40px;">
        <div class="reservation-form-container">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px;">
                <span class="eyebrow" style="background: var(--primary-soft); color: var(--primary);">Reservation details</span>
                <span style="font-size: 0.85rem; color: var(--muted); font-weight: 600;">Step 1 of 2</span>
            </div>

            @if ($vehicles->isEmpty())
                <div class="empty-state">
                    <h2>No vehicles are currently dispatchable.</h2>
                    <p>Seed the database again or free up an EV from the admin side.</p>
                </div>
            @else
                <form method="POST" action="{{ route('bookings.store') }}" class="form-grid" style="gap: 24px;">
                    @csrf

                    <div class="form-grid__full">
                        <label>
                            <span>Vehicle selection</span>
                            <div class="input-with-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                                <select name="vehicle_id" required>
                                    @foreach ($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" @selected((int) old('vehicle_id', optional($selectedVehicle)->id) === $vehicle->id)>
                                            {{ $vehicle->name }} - {{ $vehicle->battery_soc }}% SoC - {{ $vehicle->estimated_range_km }} km available
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </label>
                    </div>

                    <label>
                        <span>Pickup location</span>
                        <div class="input-with-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            <input type="text" name="pickup_location" value="{{ old('pickup_location', $customer->preferred_zone ?? optional($selectedVehicle)->location_zone) }}" required>
                        </div>
                    </label>

                    <label>
                        <span>Drop-off location</span>
                        <div class="input-with-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            <input type="text" name="dropoff_location" value="{{ old('dropoff_location') }}" required>
                        </div>
                    </label>

                    <label>
                        <span>Start date & time</span>
                        <div class="input-with-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <input type="datetime-local" name="start_at" value="{{ old('start_at', now()->addHour()->format('Y-m-d\TH:i')) }}" required>
                        </div>
                    </label>

                    <label>
                        <span>End date & time</span>
                        <div class="input-with-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <input type="datetime-local" name="end_at" value="{{ old('end_at', now()->addDays(2)->format('Y-m-d\TH:i')) }}" required>
                        </div>
                    </label>

                    <div class="form-grid__full">
                        <label>
                            <span>Estimated trip distance (km)</span>
                            <div class="input-with-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7Z"/><path d="M14 12H8"/><path d="M10 16h4"/></svg>
                                <input type="number" min="10" max="1000" name="estimated_distance_km" value="{{ old('estimated_distance_km', 80) }}" required>
                            </div>
                        </label>
                    </div>

                    <div class="form-grid__full">
                        <label>
                            <span>Trip notes & special requests</span>
                            <div class="input-with-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                <textarea name="notes" rows="4" placeholder="Optional trip purpose, charging request, or operational notes">{{ old('notes') }}</textarea>
                            </div>
                        </label>
                    </div>

                    <div class="form-grid__full" style="margin-top: 12px;">
                        <button class="btn btn-primary" type="submit" style="width: 100%; padding: 18px; font-size: 1.1rem; box-shadow: 0 20px 40px rgba(59, 130, 246, 0.25);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            Confirm reservation
                        </button>
                    </div>
                </form>
            @endif
        </div>

        <div class="sidebar-stack">
            <div class="vehicle-hero-card">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span class="eyebrow eyebrow--dark">Selected vehicle</span>
                    <span class="badge badge--primary">Live connection</span>
                </div>

                @if ($selectedVehicle)
                    <img src="{{ asset('img/electric_car.png') }}" alt="Electric Car" class="vehicle-hero-image">
                    
                    <div style="margin-top: 12px;">
                        <h2 style="color: #fff; margin: 0;">{{ $selectedVehicle->name }}</h2>
                        <p style="color: rgba(255,255,255,0.6); font-size: 0.9rem; margin-top: 4px;">{{ $selectedVehicle->location_zone }} &middot; {{ $selectedVehicle->connector_type }}</p>
                    </div>

                    <div style="margin-top: 24px;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                            <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: rgba(255,255,255,0.5);">Current battery SoC</span>
                            <strong style="font-size: 1.5rem; color: #fff;">{{ $selectedVehicle->battery_soc }}%</strong>
                        </div>
                        <div class="battery-meter-visual">
                            <div class="battery-meter-fill @if($selectedVehicle->battery_soc > 50) battery-meter-fill--good @elseif($selectedVehicle->battery_soc > 20) battery-meter-fill--warn @else battery-meter-fill--danger @endif" style="width: {{ $selectedVehicle->battery_soc }}%;"></div>
                        </div>
                        <p style="font-size: 0.8rem; color: rgba(255,255,255,0.6); margin-top: 8px;">
                            ~{{ $selectedVehicle->telemetry_summary['range_km'] }} km available range ({{ strtolower($selectedVehicle->telemetry_summary['freshness_label']) }})
                        </p>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 24px;">
                        <div class="stat-card-modern">
                            <span>Daily rate</span>
                            <strong>PHP {{ number_format((float) $selectedVehicle->daily_rate, 0) }}</strong>
                        </div>
                        <div class="stat-card-modern">
                            <span>Safe range</span>
                            <strong>{{ $previewQuote['safe_range_km'] ?? max($selectedVehicle->estimated_range_km - 25, 0) }} km</strong>
                        </div>
                    </div>
                @endif
            </div>

            @if ($previewQuote && $selectedVehicle)
                <div class="panel" style="padding: 32px; border-radius: 28px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                        <span class="eyebrow" style="background: rgba(59, 130, 246, 0.1); color: var(--primary);">Trip intelligence</span>
                        <div style="display: flex; gap: 6px;">
                            <span class="signal-pill signal-pill--{{ $selectedVehicle->telemetry_summary['confidence_tone'] }}" style="padding: 4px 10px; font-size: 0.7rem;">{{ $selectedVehicle->telemetry_summary['freshness_label'] }}</span>
                        </div>
                    </div>

                    <div class="confidence-indicator @if($previewQuote['requires_intervention']) confidence-indicator--danger @elseif($previewQuote['needs_charging_fallback']) confidence-indicator--warn @else confidence-indicator--good @endif" style="margin-bottom: 24px;">
                        <div style="flex: 1;">
                            <h3 style="margin-bottom: 4px;">
                                @if ($previewQuote['requires_intervention'])
                                    Action Required
                                @elseif ($previewQuote['needs_charging_fallback'])
                                    Charging Recommended
                                @else
                                    Route Optimized
                                @endif
                            </h3>
                            <p style="font-size: 0.85rem; line-height: 1.4; margin: 0;">
                                @if ($previewQuote['requires_intervention'])
                                    Route returns below minimum safe SoC.
                                @elseif ($previewQuote['needs_charging_fallback'])
                                    Charge stop suggested for return SoC > 30%.
                                @else
                                    Current battery snapshot covers this route safely.
                                @endif
                            </p>
                        </div>
                        <div style="text-align: right;">
                            <strong style="display: block; font-size: 1.25rem; color: var(--text);">PHP {{ number_format((float) $previewQuote['total_cost'], 2) }}</strong>
                            <span style="font-size: 0.7rem; color: var(--muted); font-weight: 700; text-transform: uppercase;">Est. Total</span>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div class="stat-card-modern" style="background: #f8fafc; border-color: #e2e8f0;">
                            <span style="color: var(--muted);">Projected Return</span>
                            <strong style="color: var(--text);">{{ $previewQuote['projected_return_soc'] }}%</strong>
                        </div>
                        <div class="stat-card-modern" style="background: #f8fafc; border-color: #e2e8f0;">
                            <span style="color: var(--muted);">Return Credit</span>
                            <strong style="color: var(--success);">PHP {{ number_format((float) $previewQuote['return_incentive_credit'], 0) }}</strong>
                        </div>
                    </div>
                </div>
            @endif

            @if ($recommendedStations->isNotEmpty())
                <div class="panel" style="padding: 32px; border-radius: 28px;">
                    <span class="eyebrow" style="margin-bottom: 20px;">Intelligent charging stops</span>
                    <div class="stack-list" style="gap: 12px;">
                        @foreach ($recommendedStations as $station)
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: #f8fafc; border-radius: 18px; border: 1px solid #e2e8f0;">
                                <div>
                                    <h4 style="margin: 0; font-size: 0.95rem;">{{ $station->name }}</h4>
                                    <p style="margin: 4px 0 0; font-size: 0.75rem; color: var(--muted);">{{ $station->confidence_summary }}</p>
                                </div>
                                <div style="text-align: right;">
                                    <span class="badge" style="background: #fff; font-size: 0.65rem;">{{ $station->live_ports }}/{{ $station->total_ports }} Ports</span>
                                    <strong style="display: block; font-size: 0.8rem; margin-top: 4px; color: @if($station->risk_label == 'Low Risk') var(--success) @else var(--warning) @endif">{{ $station->risk_label }}</strong>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="panel" style="padding: 32px; border-radius: 28px;">
                <span class="eyebrow" style="margin-bottom: 24px;">Your account</span>
                <div class="stack-list" style="gap: 16px;">
                    <div class="customer-profile-mini">
                        <div class="profile-avatar-mock">
                            {{ substr($customer->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 1.1rem;">{{ $customer->name }}</h3>
                            <p style="margin: 2px 0 0; font-size: 0.85rem; color: var(--muted);">{{ $customer->email }}</p>
                        </div>
                    </div>
                    
                    <div style="display: grid; gap: 12px; padding: 16px; background: #fff; border: 1px solid #e2e8f0; border-radius: 20px;">
                        <div style="display: flex; justify-content: space-between;">
                            <span style="font-size: 0.8rem; color: var(--muted); font-weight: 600;">Status</span>
                            <span class="badge @if($customer->license_verified) badge--good @else badge--warn @endif" style="font-size: 0.7rem;">
                                {{ $customer->license_verified ? 'Verified Driver' : 'Pending Verification' }}
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="font-size: 0.8rem; color: var(--muted); font-weight: 600;">Preferred Zone</span>
                            <span style="font-size: 0.85rem; font-weight: 700;">{{ $customer->preferred_zone ?? 'General Fleet' }}</span>
                        </div>
                    </div>

                    <div style="padding: 16px; background: #fffbeb; border-radius: 18px; border: 1px solid #fef3c7;">
                        <div style="display: flex; gap: 10px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <div>
                                <h4 style="margin: 0; font-size: 0.85rem; color: #92400e;">EV Pro Tip</h4>
                                <p style="margin: 4px 0 0; font-size: 0.75rem; color: #b45309; line-height: 1.4;">
                                    Return above 40% SoC to unlock the mock return credit and avoid deep discharge fees.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
