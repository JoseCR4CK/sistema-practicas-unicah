<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class ClosingSchema
{
    public static function available(string $domain): bool
    {
        $tables = match ($domain) {
            'monographs' => ['monographs', 'monograph_versions', 'monograph_reviews'],
            'committees' => ['academic_committees', 'committee_members'],
            'defenses' => ['defenses', 'defense_options', 'defense_availabilities', 'defense_class_blocks', 'academic_committees', 'committee_members'],
        };
        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    }

    public function handle(Request $request, Closure $next, string $domain): Response
    {
        abort_unless($request->user()?->isActive(), 403, 'El usuario no se encuentra activo.');
        abort_unless(self::available($domain), 503, 'El módulo requiere completar la actualización de la base de datos.');

        return $next($request);
    }
}
