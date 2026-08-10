<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

/**
 * Controller login DUMMY.
 *
 * Tidak melakukan pengecekan kredensial ke database / tabel users.
 * Form apapun yang dikirim (asal email & password terisi) akan langsung
 * dianggap berhasil dan diarahkan ke dashboard. Gunakan ini hanya untuk
 * keperluan demo/prototipe tampilan, bukan untuk produksi.
 */
class DummyLoginController extends Controller
{
    public function show(): \Illuminate\View\View
    {
        return view('auth.login');
    }

    public function attempt(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Dummy: tidak ada pengecekan ke database, langsung dianggap login sukses.
        session([
            'dummy_logged_in' => true,
            'dummy_user_name' => 'Admin',
            'dummy_user_email' => $request->input('email'),
        ]);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['dummy_logged_in', 'dummy_user_name', 'dummy_user_email']);

        return redirect()->route('login');
    }
}