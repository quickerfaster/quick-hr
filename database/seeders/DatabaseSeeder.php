<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Admin\Database\Seeders\QFDatabaseSeeder;
use App\Modules\Hr\Database\Seeders\EmployeeWithDependenciesSeeder;
use App\Modules\Hr\Database\Seeders\MultiCompanyPayrollTestDataSeeder;
use Database\Seeders\SystemSeeder;
use Database\Seeders\UserSeeder;
use QuickerFaster\UILibrary\Services\AccessControl\AccessControlPermissionService;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            QFDatabaseSeeder::class,
            SystemSeeder::class,
            UserSeeder::class,
            // HR Module: Creates companies, departments, locations, and 5,000 employees
            EmployeeWithDependenciesSeeder::class,
            // HR Module: Multi-company payroll test data (4 companies, pay schedules, 100 employees, adjustments)
            MultiCompanyPayrollTestDataSeeder::class,
        ]);

        AccessControlPermissionService::seedPermissionNames();

    }
}
