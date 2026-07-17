<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

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
        // Morph map ensures both User model classes resolve to the same
        // morph type in Spatie permission tables (model_has_roles, etc.)
        Relation::morphMap([
            'user' => \App\Models\User::class,
            'system' => \App\Modules\System\Models\System::class,
        ]);
    }
}
