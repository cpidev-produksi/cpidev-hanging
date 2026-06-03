<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt(['username' => $data['username'], 'password' => $data['password']], true)) {
            $request->session()->regenerate();

            // $roleSlug = Auth::user()?->role?->slug;

            // if (in_array($roleSlug, ['supervisor', 'superadmin'], true)) {
            //     return redirect()->route('menu.index');
            // }

            // role lain -> dashboard
            return redirect()->route('menu.index');
        }

        return back()->withErrors(['username' => 'Username atau password salah.'])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
