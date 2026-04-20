@extends('layouts.app')

@section('title', 'ECROS Admin')

@section('content')
    <section class="section-heading section-heading--intro">
        <div>
            <span class="eyebrow">Fleet command center</span>
            <h1>Admin telemetry, charging, and fleet readiness in a cleaner control surface.</h1>
            <p class="lead">
                The customer-facing polish now carries into the operations layer while preserving dark-card contrast for critical monitoring.
            </p>
        </div>
    </section>

    <section class="metric-grid">
        <div class="metric-card">
            <span>Total revenue</span>
            <strong>PHP {{ number_format((float) $stats['revenue'], 2) }}</strong>
        </div>
        <div class="metric-card">
            <span>Active rentals</span>
            <strong>{{ $stats['activeRentals'] }}</strong>
        </div>
        <div class="metric-card">
            <span>Average battery health</span>
            <strong>{{ $stats['avgHealth'] }}%</strong>
        </div>
        <div class="metric-card">
            <span>Charging jobs queued</span>
            <strong>{{ $stats['gridReady'] }}</strong>
        </div>
    </section>

    <section class="metric-grid">
        <div class="metric-card">
            <span>Downtime rate</span>
            <strong>{{ $analytics['downtime_rate'] }}%</strong>
        </div>
        <div class="metric-card">
            <span>Failed booking rate</span>
            <strong>{{ $analytics['failed_booking_rate'] }}%</strong>
        </div>
        <div class="metric-card">
            <span>Charger wait time</span>
            <strong>{{ $analytics['charger_wait_time'] }} min</strong>
        </div>
        <div class="metric-card">
            <span>Stale data rate</span>
            <strong>{{ $analytics['stale_data_rate'] }}%</strong>
        </div>
    </section>

    <section class="section section--split">
        <div class="panel panel--dark">
            <div class="section-heading">
                <div>
                    <span class="eyebrow eyebrow--dark">Operational alerts</span>
                    <h2>Issues needing attention</h2>
                </div>
            </div>
            <div class="stack-list">
                @forelse ($alerts as $alert)
                    <article class="alert-card">
                        <span class="alert-card__level">{{ $alert['level'] }}</span>
                        <h3>{{ $alert['title'] }}</h3>
                        <p>{{ $alert['copy'] }}</p>
                    </article>
                @empty
                    <article class="list-card">
                        <div>
                            <h3>No active alerts</h3>
                            <p>The fleet is within the current mock thresholds.</p>
                        </div>
                    </article>
                @endforelse
            </div>
        </div>

        <div class="panel">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Charging scheduler</span>
                    <h2>Queue and live charging sessions</h2>
                </div>
            </div>
            <div class="stack-list">
                @foreach ($chargingQueue as $session)
                    <article class="list-card">
                        <div>
                            <h3>{{ $session->vehicle->name }}</h3>
                            <p>{{ $session->chargingStation->name }}</p>
                        </div>
                        <div class="list-card__meta">
                            <strong>{{ ucfirst(str_replace('_', ' ', $session->status)) }}</strong>
                            <span>Target {{ $session->target_soc }}%</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section section--split">
        <div class="panel">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Hub-first logistics</span>
                    <h2>Relocation recommendations</h2>
                </div>
            </div>
            <div class="stack-list">
                @forelse ($relocationRecommendations as $recommendation)
                    <article class="list-card">
                        <div>
                            <h3>{{ $recommendation['title'] }}</h3>
                            <p>{{ $recommendation['copy'] }}</p>
                        </div>
                        <div class="list-card__meta">
                            <strong>{{ $recommendation['label'] }}</strong>
                        </div>
                    </article>
                @empty
                    <article class="list-card">
                        <div>
                            <h3>No relocations suggested</h3>
                            <p>Current low-charge pressure is within the seeded mock thresholds.</p>
                        </div>
                    </article>
                @endforelse
            </div>
        </div>

        <div class="panel panel--dark">
            <div class="section-heading">
                <div>
                    <span class="eyebrow eyebrow--dark">Research toggle</span>
                    <h2>V2G mode and smart charging baseline</h2>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.settings.v2g') }}" class="settings-form">
                @csrf
                <label class="toggle-row">
                    <span>
                        <strong>Vehicle-to-Grid research mode</strong>
                        <small>{{ $v2gEnabled ? 'Enabled for simulation only' : 'Disabled. Smart charging remains the production baseline.' }}</small>
                    </span>
                    <input type="checkbox" name="enabled" value="1" @checked($v2gEnabled)>
                </label>
                <button class="btn btn-secondary" type="submit">Update setting</button>
            </form>
        </div>
    </section>

    <section class="section section--split">
        <div class="panel">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Remote command security</span>
                    <h2>Signed lock, unlock, and immobilize commands</h2>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.remote-commands.store') }}" class="form-grid">
                @csrf
                <label>
                    <span>Vehicle</span>
                    <select name="vehicle_id" required>
                        @foreach ($commandableVehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->name }} - {{ ucfirst($vehicle->status) }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>Command</span>
                    <select name="command_type" required>
                        <option value="lock">Lock</option>
                        <option value="unlock">Unlock</option>
                        <option value="immobilize">Immobilize</option>
                    </select>
                </label>
                <label class="form-grid__full">
                    <span>Justification</span>
                    <input type="text" name="justification" value="{{ old('justification') }}" placeholder="Operational reason for this sensitive action">
                </label>
                <label class="form-grid__full">
                    <span>Step-up password</span>
                    <input type="password" name="current_password" autocomplete="current-password" required>
                </label>
                <div class="form-grid__full">
                    <button class="btn btn-primary" type="submit">Sign and execute command</button>
                </div>
            </form>

            <div class="stack-list">
                @foreach ($remoteCommands as $command)
                    <article class="list-card">
                        <div>
                            <h3>{{ ucfirst($command->command_type) }} - {{ $command->vehicle->name }}</h3>
                            <p>{{ $command->requester->name }} &middot; {{ $command->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="list-card__meta">
                            <strong>{{ ucfirst($command->result_status) }}</strong>
                            <span>{{ $command->signature ? 'Signed token issued' : 'Rejected before signing' }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

        <div class="panel panel--dark">
            <div class="section-heading">
                <div>
                    <span class="eyebrow eyebrow--dark">Security monitoring</span>
                    <h2>Recent anomaly events</h2>
                </div>
            </div>
            <div class="stack-list">
                @foreach ($recentSecurityEvents as $event)
                    <article class="list-card">
                        <div>
                            <h3>{{ str_replace('_', ' ', ucfirst($event->event_type)) }}</h3>
                            <p>{{ $event->description }}</p>
                        </div>
                        <div class="list-card__meta">
                            <strong>{{ ucfirst($event->severity) }}</strong>
                            <span>{{ $event->detected_at->diffForHumans() }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="panel">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Fleet monitor</span>
                <h2>Vehicle health and readiness</h2>
            </div>
        </div>
        <div class="stack-list">
            @foreach ($vehicles as $vehicle)
                <article class="list-card">
                    <div>
                        <h3>{{ $vehicle->name }}</h3>
                        <p>{{ ucfirst($vehicle->status) }} &middot; {{ $vehicle->location_zone }} &middot; {{ $vehicle->telemetry_summary['signal_label'] }}</p>
                    </div>
                    <div class="list-card__meta">
                        <strong>{{ $vehicle->battery_soc }}% battery / {{ $vehicle->battery_health }}% health / {{ $vehicle->telemetry_summary['freshness_label'] }}</strong>
                        <span>
                            @if ($vehicle->chargingSessions->isNotEmpty())
                                {{ ucfirst(str_replace('_', ' ', $vehicle->chargingSessions->first()->status)) }} at {{ $vehicle->chargingSessions->first()->chargingStation->name }}
                            @elseif ($vehicle->bookings->isNotEmpty())
                                Booking {{ $vehicle->bookings->first()->reference }}
                            @else
                                Ready for dispatch
                            @endif
                            &middot; {{ $vehicle->is_immobilized ? 'Immobilized' : ($vehicle->is_locked ? 'Locked' : 'Unlocked') }}
                        </span>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="panel">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Simulation lab</span>
                <h2>Scaling scenarios for thesis validation</h2>
            </div>
        </div>
        <div class="simulation-grid">
            @foreach ($simulationScenarios as $scenario)
                <article class="simulation-card">
                    <div>
                        <h3>{{ $scenario['title'] }}</h3>
                        <p>{{ $scenario['description'] }}</p>
                    </div>
                    <div class="stack-list">
                        @foreach ($scenario['runs'] as $run)
                            <article class="list-card">
                                <div>
                                    <h3>{{ $run['label'] }}</h3>
                                    <p>Utilization {{ $run['utilization_rate'] }}% &middot; downtime {{ $run['downtime_rate'] }}%</p>
                                </div>
                                <div class="list-card__meta">
                                    <strong>{{ $run['charger_wait_minutes'] }} min wait</strong>
                                    <span>{{ $run['range_risk_incidents'] }} range-risk incidents</span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="panel">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Recent reservations</span>
                <h2>Latest booking activity</h2>
            </div>
        </div>
        <div class="stack-list">
            @foreach ($recentBookings as $booking)
                <article class="list-card">
                    <div>
                        <h3>{{ $booking->reference }}</h3>
                        <p>{{ $booking->user->name }} &middot; {{ $booking->vehicle->name }}</p>
                    </div>
                    <div class="list-card__meta">
                        <strong>{{ ucfirst($booking->status) }}</strong>
                        <span>PHP {{ number_format((float) $booking->total_cost, 2) }}</span>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endsection
