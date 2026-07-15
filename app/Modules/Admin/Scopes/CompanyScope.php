<?php

namespace App\Modules\Admin\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Session;

class CompanyScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * Only filters when a current_company_id is set in the session.
     * Super admins without a selected company see all data.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $companyId = Session::get('current_company_id');

        if ($companyId) {
            $table = $model->getTable();
            $builder->where("{$table}.company_id", $companyId);
        }
    }

    /**
     * Extend the query builder with a method to bypass the company scope.
     *
     * Usage: Model::withoutCompanyScope()->get()
     */
    public function extend(Builder $builder): void
    {
        $builder->macro('withoutCompanyScope', function (Builder $builder) {
            return $builder->withoutGlobalScope(static::class);
        });
    }
}