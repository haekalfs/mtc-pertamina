<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        // Attempt authentication without logging in
        if (Auth::validate($credentials)) {
            $user = Auth::getProvider()->retrieveByCredentials($credentials);

            if ($user->two_factor_secret) {
                // Store user ID in session temporarily
                session(['two_factor_auth' => $user->id]);
                return back()->with('two-factor', true);
            }

            // Log in user normally if no 2FA
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended(RouteServiceProvider::HOME);
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function verifyTwoFactor(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);

        $user = \App\Models\User::find(session('two_factor_auth'));

        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'Invalid session. Please log in again.']);
        }

        if ($user->verifyTwoFactorCode($request->code)) {
            // Log in user
            Auth::login($user);
            session()->forget('two_factor_auth'); // Remove temporary session
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        return back()->withErrors(['code' => 'Invalid authentication code']);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
