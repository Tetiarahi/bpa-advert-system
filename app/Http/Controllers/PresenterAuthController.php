<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Facades\LogActivity;
use Illuminate\Support\Facades\Hash;
use App\Models\Presenter;

class PresenterAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('presenter.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('presenter')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // Store login time for content filtering
            session(['login_time' => now()]);

            // Get the authenticated presenter
            $presenter = Auth::guard('presenter')->user();

            // Log the successful login activity
            activity('presenter_auth')
                ->causedBy($presenter)
                ->performedOn($presenter)
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'login_time' => now()->toDateTimeString(),
                    'shift' => $presenter->shift,
                ])
                ->log('Presenter logged in');

            return redirect()->intended('/presenter/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        // Get the presenter before logging out
        $presenter = Auth::guard('presenter')->user();

        if ($presenter) {
            // Calculate session duration
            $loginTime = session('login_time', now());
            $sessionDuration = now()->diffInMinutes($loginTime);

            // Log the logout activity
            activity('presenter_auth')
                ->causedBy($presenter)
                ->performedOn($presenter)
                ->withProperties([
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'logout_time' => now()->toDateTimeString(),
                    'session_duration_minutes' => $sessionDuration,
                    'shift' => $presenter->shift,
                ])
                ->log('Presenter logged out');
        }

        Auth::guard('presenter')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
