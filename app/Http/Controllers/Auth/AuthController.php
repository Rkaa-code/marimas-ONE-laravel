<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
<<<<<<< HEAD:app/Http/Controllers/auth/AuthController.php
use App\Models\AuditLog;
=======
>>>>>>> 2186d5038d8114b2218ee963b7ceb6116291fd82:app/Http/Controllers/Auth/AuthController.php
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function show(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['email' => 'Email atau password salah.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
<<<<<<< HEAD:app/Http/Controllers/auth/AuthController.php

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'login',
            'description' => Auth::user()->name . ' login ke sistem',
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

=======
>>>>>>> 2186d5038d8114b2218ee963b7ceb6116291fd82:app/Http/Controllers/Auth/AuthController.php
        flash()
            ->success('Selamat datang, ' . Auth::user()->name . '!');
        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
<<<<<<< HEAD:app/Http/Controllers/auth/AuthController.php
        if (Auth::check()) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'logout',
                'description' => Auth::user()->name . ' logout dari sistem',
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ]);
        }

=======
>>>>>>> 2186d5038d8114b2218ee963b7ceb6116291fd82:app/Http/Controllers/Auth/AuthController.php
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}