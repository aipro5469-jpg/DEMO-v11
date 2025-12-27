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
        \App\Models\Campaign::observe(\App\Observers\CampaignObserver::class);
        \App\Models\Task::observe(\App\Observers\TaskObserver::class);
        \App\Models\TaskCheckpoint::observe(\App\Observers\TaskCheckpointObserver::class);

        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });
    }
}
