<?php

namespace App\Modules\System\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

use App\Modules\Hr\Models\Company;
use App\Modules\Hr\Models\Shift;
use App\Modules\Hr\Models\WorkPattern;
use App\Modules\Hr\Models\AttendancePolicy;
use App\Modules\Hr\Models\Location;
use App\Modules\Hr\Models\Department;

class DefaultDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create a placeholder company (will be updated by the controller)
        $company = Company::firstOrCreate(
            ['subdomain' => 'placeholder'],
            [
                'name' => 'Placeholder Company',
                'status' => 'pending',
                'billing_email' => 'placeholder@example.com',
                'timezone' => 'UTC',
                'currency_code' => 'USD',
                'is_placeholder' => true,
            ]
        );

        // 2. Create default shift
        $defaultShift = Shift::firstOrCreate(
            ['code' => 'STD', 'company_id' => $company->id],
            [
                'name' => 'Standard Day Shift',
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'duration_hours' => 8.0,
                'is_overnight' => false,
                'is_active' => true,
                'is_default' => true,
                'shift_category' => 'regular',
            ]
        );

        // 3. Create default work pattern
        $defaultWorkPattern = WorkPattern::firstOrCreate(
            ['code' => 'M-F', 'company_id' => $company->id],
            [
                'name' => 'Monday-Friday',
                'shift_id' => $defaultShift->id,
                'applicable_days' => '1,2,3,4,5', // Monday to Friday
                'pattern_type' => 'recurring',
                'effective_date' => Carbon::now(),
                'is_active' => true,
                'is_default' => true,
            ]
        );

        // 4. Create default attendance policy
        $defaultPolicy = AttendancePolicy::firstOrCreate(
            ['code' => 'DEFAULT', 'company_id' => $company->id],
            [
                'name' => 'Default Attendance Policy',
                'effective_date' => Carbon::now(),
                'is_active' => true,
                'is_default' => true,
                'grace_period_minutes' => 5,
                'early_departure_grace_minutes' => 5,
                'overtime_daily_threshold_hours' => 8.0,
                'overtime_weekly_threshold_hours' => 40.0,
                'max_daily_overtime_hours' => 4.0,
                'overtime_multiplier' => 1.5,
                'double_time_threshold_hours' => 12.0,
                'double_time_multiplier' => 2.0,
                'requires_break_after_hours' => 5.0,
                'break_duration_minutes' => 30,
                'unpaid_break_minutes' => 0,
                'applies_to_shift_categories' => json_encode(['regular']),
            ]
        );

        // 5. Create default location
        $defaultLocation = Location::firstOrCreate(
            ['code' => 'DEFAULT-HQ', 'company_id' => $company->id],
            [
                'name' => 'Default Headquarters',
                'is_active' => true,
                'is_headquarters' => true,
                'address_line_1' => 'Default Address',
                'city' => 'Default City',
                'country_code' => 'US',
                'timezone' => 'America/New_York',
            ]
        );

        // 6. Create default department
        $defaultDepartment = Department::firstOrCreate(
            ['code' => 'GEN', 'company_id' => $company->id],
            [
                'name' => 'General',
                'is_active' => true,
            ]
        );
    }
}
