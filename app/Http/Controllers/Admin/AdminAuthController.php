<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminAuthController extends Controller
{
    public function create()
    {
        if (auth()->check() && auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        return Inertia::render('Admin/Login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! auth()->attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Credenciais inválidas.',
            ])->onlyInput('email');
        }

        $user = $request->user();

        if (! $user->hasRole('admin')) {
            auth()->logout();

            return back()->withErrors([
                'email' => 'Você não tem permissão para acessar o painel administrativo.',
            ])->onlyInput('email');
        }

        return redirect()->route('admin.dashboard');
    }

    public function destroy(Request $request)
    {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
