<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login', [
            'demoAccounts' => [
                [
                    'label' => 'Admin access',
                    'email' => 'ops.manager@ecros.test',
                    'password' => 'password',
                    'description' => 'Fleet operations dashboard and full booking visibility.',
                ],
                [
                    'label' => 'Customer access',
                    'email' => 'dane@ecros.test',
                    'password' => 'password',
                    'description' => 'Customer dashboard, my trips, and protected booking flow.',
                ],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();

        $user = $request->user();

        return redirect()->intended(
            $user->isAdmin() ? route('admin.dashboard') : route('dashboard')
        );
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'You have been signed out.');
    }
}
