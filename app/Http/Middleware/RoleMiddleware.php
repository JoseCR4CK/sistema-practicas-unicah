<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!$user->isActive()) {
            auth()->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'dni' => 'Tu usuario se encuentra inactivo.',
                ]);
        }

        $userRole = $user->role?->name;

        if (!in_array($userRole, $roles, true)) {
            abort(403, 'No tienes autorización para acceder a este módulo.');
        }

        return $next($request);
    }
}
