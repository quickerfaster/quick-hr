<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Initialize company session on login for multi-tenancy
        Event::listen(Login::class, function (Login $event) {
            $user = $event->user;

            // Only set if not already present (preserves explicit user choice)
            if (!Session::has('current_company_id')) {
                $config = config('quicker-faster-ui.multitenancy', []);
                $allCompaniesRoles = $config['all_companies_roles'] ?? ['super_admin'];
                $defaultMode = $config['default_mode'] ?? 'first';

                $companyId = null;

                // Users who can see "All Companies" default to that mode
                if ($allCompaniesRoles === '*' || $allCompaniesRoles === ['*'] || $user->hasAnyRole((array) $allCompaniesRoles)) {
                    $companyId = ($defaultMode === 'all') ? 0 : null;
                }

                // If not set to "All Companies", find the user's company.
                // Employee record is the source of truth; fall back to user.company_id.
                if ($companyId === null) {
                    $companyId = optional($user->employee)->company_id
                        ?? $user->company_id;
                }

                if ($companyId !== null) {
                    Session::put('current_company_id', $companyId);
                }
            }
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
