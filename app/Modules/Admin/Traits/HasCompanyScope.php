<?php

namespace App\Modules\Admin\Traits;

use App\Modules\Admin\Scopes\CompanyScope;

/**
 * Trait HasCompanyScope
 *
 * Apply this trait to any Eloquent model that should be automatically
 * scoped by company_id based on the current session's current_company_id.
 *
 * When current_company_id is set in the session, all queries will
 * automatically filter by that company_id.
 *
 * When current_company_id is NOT set (e.g., super_admin without a
 * selected company), no filtering is applied — all records are visible.
 *
 * Usage:
 *   use HasCompanyScope;
 *
 * To bypass the scope for a specific query:
 *   Model::withoutCompanyScope()->get();
 *   Model::withoutGlobalScope(CompanyScope::class)->get();
 */
trait HasCompanyScope
{
    /**
     * Boot the trait — registers the CompanyScope global scope.
     */
    protected static function bootHasCompanyScope(): void
    {
        static::addGlobalScope(new CompanyScope());
    }
}
