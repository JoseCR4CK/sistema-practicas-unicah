<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer(['layouts.app', 'coordinacion.dashboard'], function ($view) {
            $view->with('pendingAccessRequests', \App\Models\AccessRequest::whereIn('status', ['PENDING', 'IN_REVIEW', 'CORRECTION_REQUIRED'])->count());
            $view->with('pendingRequests', \App\Models\PracticeRequest::where('status', 'Pendiente')->count());
            $view->with('attentionDocuments', \App\Models\AcademicDocument::whereNotNull('practice_id')->whereHas('currentVersion', fn ($query) => $query
                ->whereIn('status', [\App\Models\DocumentVersion::PENDING, \App\Models\DocumentVersion::CORRECTION]))->count());
        });
    }
}
