<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Session;

class login extends Controller
{
    public function showForm(): View
    {
        return View('index');
    }

    public function loginUser(Request $request): RedirectResponse|View
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Regenerate session to prevent session fixation attacks
            $request->session()->regenerate();

            $user = Auth::user();

            // Update user status
            $user->update([
                'is_online' => true,
                'last_activity' => now(),
            ]);

            // Role-based redirection (case-insensitive)
            $role = strtolower($user->Role ?? '');

            return match ($role) {
                'owner' => redirect()->intended(route('owner')),
                'manager' => redirect()->intended(route('manager')),
                'customer' => redirect()->intended(route('customer')),
                default => redirect()->route('Login')->with('status', 'Invalid user role'),
            };
        }

        return back()->withInput()->with('status', 'Invalid Credentials or User not Found');
    }
    public function logout(): RedirectResponse
    {
        // Mark user as offline before logging out
        if (Auth::check()) {
            Auth::user()->update([
                'is_online' => false,
                'last_activity' => null,
            ]);
        }

        Session::flush();
        Auth::logout();

        return redirect(route('Login'));
    }
}
