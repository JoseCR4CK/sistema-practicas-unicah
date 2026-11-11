<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class ClosingSchema
{
    public function handle(Request $request, Closure $next, string $domain): Response
    {
        $tables = match ($domain) {
            'monographs' => ['monographs', 'monograph_versions', 'monograph_reviews'],
            'committees' => ['academic_committees', 'committee_members'],
            'defenses' => ['defenses', 'defense_options', 'defense_availabilities', 'defense_class_blocks', 'academic_committees', 'committee_members'],
        };
        foreach ($tables as $table) {
            abort_unless(Schema::hasTable($table), 503, 'El módulo requiere completar la actualización de la base de datos.');
        }

        return $next($request);
    }
}
