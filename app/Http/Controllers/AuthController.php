<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        return view('auth.login');
    }


    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'dni' => [
                'required',
                'string',
            ],

            'password' => [
                'required',
                'string',
            ],
        ]);

        if (!Auth::attempt($credentials)) {
            return back()
                ->withErrors([
                    'dni' => 'El número de cuenta/DNI o la contraseña son incorrectos.',
                ])
                ->onlyInput('dni');
        }


        $request->session()->regenerate();


        if (!Auth::user()->isActive()) {

            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors([
                    'email' => 'Este usuario se encuentra inactivo.',
                ]);
        }


        return $this->redirectByRole();
    }


    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route('login');
    }


    private function redirectByRole(): RedirectResponse
    {
        $user = Auth::user();

        $role =
            $user
                ->role()
                ->value('name');


        return match ($role) {

            'ROOT' =>
                redirect()
                    ->route('coordinacion.dashboard'),

            'COORDINACION' =>
                redirect()
                    ->route('coordinacion.dashboard'),

            'DOCENTE_SEGUIMIENTO' =>
                redirect()
                    ->route('docente.dashboard'),

            'ESTUDIANTE' =>
                redirect()
                    ->route('estudiante.dashboard'),

            'JEFE_INMEDIATO' =>
                redirect()
                    ->route('jefe.dashboard'),

            'PERSONAL_CAMPUS' =>
                $this->logoutUnsupportedRole(),

            default =>
                $this->logoutUnsupportedRole(),
        };
    }


    private function logoutUnsupportedRole(): RedirectResponse
    {
        Auth::logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->withErrors([
                'email' => 'El rol de este usuario aún no tiene un módulo habilitado.',
            ]);
    }
}