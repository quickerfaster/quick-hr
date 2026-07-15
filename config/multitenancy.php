<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Multi-Tenancy Configuration
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Tenant Model
    |--------------------------------------------------------------------------
    */
    'tenant_model' => \App\Modules\Admin\Models\Company::class,

    /*
    |--------------------------------------------------------------------------
    | Tenant Foreign Key
    |--------------------------------------------------------------------------
    | The foreign key column name used on tenant-scoped tables.
    */
    'tenant_foreign_key' => 'company_id',

    /*
    |--------------------------------------------------------------------------
    | Tenant Column
    |--------------------------------------------------------------------------
    | The column on the tenant model used as the tenant identifier.
    */
    'tenant_column' => 'id',

    /*
    |--------------------------------------------------------------------------
    | Tenant Identification
    |--------------------------------------------------------------------------
    | How tenants are identified. Options: 'subdomain', 'domain', 'path', 'header'
    */
    'identification' => [
        'method' => env('TENANT_IDENTIFICATION', 'subdomain'),
        'header' => 'X-Company-Id',
    ],

    /*
    |--------------------------------------------------------------------------
    | Global Scope
    |--------------------------------------------------------------------------
    | Whether to automatically apply tenant scoping to all models.
    */
    'global_scope' => env('MULTITENANCY_GLOBAL_SCOPE', true),

    /*
    |--------------------------------------------------------------------------
    | Tenant-Aware Models
    |--------------------------------------------------------------------------
    | Models that should be automatically scoped to the current tenant.
    | '*' means all models that have the tenant_foreign_key column.
    */
    'tenant_aware_models' => '*',
];
