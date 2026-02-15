<?php

namespace App\Http\Controllers;

use App\Models\KantinUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KantinAuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($request->session()->has('kantin_user_id')) {
            return redirect('/app/home');
        }

        return view('app.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = KantinUser::query()->where('username', $credentials['username'])->first();
        if (!$user || !password_verify($credentials['password'], $user->password)) {
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->withInput();
        }

        $request->session()->regenerate();
        $request->session()->put('kantin_user_id', $user->id);
        $request->session()->put('username_kantin', $user->username);
        $request->session()->put('level_kantin', (int) $user->level);
        $request->session()->put('id_kantin', (int) $user->id);
        $request->session()->put('nama_toko_kantin', $user->Kios);

        return redirect('/app/home');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/app/login');
    }
}
