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
     */
    public function run(): void
    {
        // Core seeders – always run (safe and idempotent)
        $this->call([
            QFDatabaseSeeder::class,
            SystemSeeder::class,
            UserSeeder::class,
            // Add any other production‑safe seeders here
        ]);

        // Test data seeders – only in non‑production environments
        $this->seedTestDataIfAllowed();

        // Permissions (must run after roles are seeded)
        AccessControlPermissionService::seedPermissionNames();
    }

    /**
     * Conditionally seed test data based on environment or --force flag.
     */
    protected function seedTestDataIfAllowed(): void
    {
        $allowed = app()->environment('local', 'staging', 'development')
            || ($this->command && $this->command->option('force'));

        if (!$allowed) {
            $this->command->warn('Skipping test data seeders (not in local/staging/development and --force not set).');
            return;
        }

        $this->call(EmployeeWithDependenciesSeeder::class);
        $this->call(MultiCompanyPayrollTestDataSeeder::class);
    }
}
