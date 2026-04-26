@extends('layouts.app')

@section('title', 'Help Center | ECROS')

@section('content')
    <div style="display: grid; gap: 40px; max-width: 1000px; margin: 0 auto;">
        <div style="text-align: center; padding: 60px 20px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-strong) 100%); border-radius: 32px; color: #fff; position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: url('https://www.transparenttextures.com/patterns/cubes.png'); opacity: 0.1;"></div>
            <div style="position: relative; z-index: 1;">
                <h1 style="font-size: 3rem; margin-bottom: 16px;">How can we help?</h1>
                <p style="font-size: 1.25rem; opacity: 0.9; margin-bottom: 32px;">Search our knowledge base or browse categories below.</p>
                <div style="max-width: 600px; margin: 0 auto; position: relative;">
                    <input type="text" placeholder="Search for articles (e.g. how to charge, payment methods...)" style="width: 100%; padding: 20px 60px; border-radius: 20px; border: none; font-size: 1.1rem; box-shadow: 0 20px 40px rgba(0,0,0,0.2); outline: none; color: var(--text);">
                    <svg style="position: absolute; left: 24px; top: 50%; transform: translateY(-50%); color: var(--muted);" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
            <article class="panel" style="padding: 32px; transition: transform 0.2s ease; cursor: pointer;">
                <div style="width: 56px; height: 56px; background: #eff6ff; color: #3b82f6; border-radius: 16px; display: grid; place-items: center; margin-bottom: 24px;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                </div>
                <h3 style="margin-bottom: 12px;">Booking & Fleet</h3>
                <p style="color: var(--muted); font-size: 0.95rem; line-height: 1.5;">Learn how to book a car, extend your trip, or cancel a reservation.</p>
                <a href="#" class="text-link" style="margin-top: 20px; display: inline-block;">12 Articles &rarr;</a>
            </article>

            <article class="panel" style="padding: 32px; transition: transform 0.2s ease; cursor: pointer;">
                <div style="width: 56px; height: 56px; background: #fff7ed; color: #f97316; border-radius: 16px; display: grid; place-items: center; margin-bottom: 24px;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 7h1a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-1"/><path d="M6 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><line x1="11" y1="7" x2="13" y2="7"/><line x1="11" y1="17" x2="13" y2="17"/><path d="m10 11 2 2 2-2"/></svg>
                </div>
                <h3 style="margin-bottom: 12px;">Charging & Battery</h3>
                <p style="color: var(--muted); font-size: 0.95rem; line-height: 1.5;">Guide to using charging stations, finding ports, and battery management.</p>
                <a href="#" class="text-link" style="margin-top: 20px; display: inline-block;">8 Articles &rarr;</a>
            </article>

            <article class="panel" style="padding: 32px; transition: transform 0.2s ease; cursor: pointer;">
                <div style="width: 56px; height: 56px; background: #f0fdf4; color: #22c55e; border-radius: 16px; display: grid; place-items: center; margin-bottom: 24px;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                </div>
                <h3 style="margin-bottom: 12px;">Payments & Refunds</h3>
                <p style="color: var(--muted); font-size: 0.95rem; line-height: 1.5;">Information about rates, billing, deposit returns, and payment issues.</p>
                <a href="#" class="text-link" style="margin-top: 20px; display: inline-block;">6 Articles &rarr;</a>
            </article>
        </div>

        <div class="panel" style="padding: 40px;">
            <h2 style="margin-bottom: 32px;">Frequently Asked Questions</h2>
            <div style="display: grid; gap: 16px;">
                @foreach ([
                    ['q' => 'How do I unlock the car?', 'a' => 'Once your booking starts, you can unlock the car via the "My Trips" section in the ECROS app by clicking the "Unlock" remote command button.'],
                    ['q' => 'What happens if the battery runs low?', 'a' => 'The app will notify you when battery drops below 20%. You can use the map to find the nearest charging station compatible with your vehicle.'],
                    ['q' => 'Is insurance included in the rental?', 'a' => 'Yes, all ECROS rentals include comprehensive insurance. However, a standard deductible applies in case of damage.'],
                    ['q' => 'Can I end my trip early?', 'a' => 'Yes, you can return the car early. Your final bill will be adjusted based on the actual duration and distance covered.']
                ] as $faq)
                    <details style="border: 1px solid var(--line); border-radius: 16px; padding: 20px; background: var(--bg-soft);">
                        <summary style="font-weight: 700; cursor: pointer; display: flex; justify-content: space-between; align-items: center; list-style: none;">
                            {{ $faq['q'] }}
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                        </summary>
                        <p style="margin-top: 16px; color: var(--muted); line-height: 1.6;">{{ $faq['a'] }}</p>
                    </details>
                @endforeach
            </div>
        </div>

        <div style="background: #0f172a; border-radius: 32px; padding: 48px; color: #fff; text-align: center;">
            <h2 style="margin-bottom: 16px;">Still need help?</h2>
            <p style="opacity: 0.7; margin-bottom: 32px;">Our support team is available 24/7 to assist you with any issues.</p>
            <div style="display: flex; gap: 16px; justify-content: center;">
                <button class="btn btn-primary" style="padding: 16px 32px;">Chat with Support</button>
                <button class="btn btn-secondary" style="padding: 16px 32px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff;">Email Us</button>
            </div>
        </div>
    </div>
@endsection
