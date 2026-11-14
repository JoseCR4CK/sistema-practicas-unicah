<?php

use App\Http\Controllers\Api\CompanyController;
use App\Http\Middleware\CompanyApiAccess;
use Illuminate\Support\Facades\Route;

// Sesiones y CSRF existentes; no requiere tokens ni paquetes adicionales.
Route::middleware(['web', CompanyApiAccess::class])->name('api.')->group(function (): void {
    Route::apiResource('companies', CompanyController::class);
});
