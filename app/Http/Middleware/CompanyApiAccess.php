<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyApiAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user(), 401, 'Autenticación requerida.');
        abort_unless($request->user()->isActive()
            && in_array($request->user()->role?->name, ['COORDINACION', 'ROOT'], true),
            403, 'No tienes autorización para gestionar empresas.');

        return $next($request);
    }
}
