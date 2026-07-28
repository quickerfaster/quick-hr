<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payroll Processing Settings
    |--------------------------------------------------------------------------
    |
    | These values control how the payroll batch jobs behave.
    | Adjust them to fit your server's capacity and cron limitations.
    |
    */

    // Number of employees per batch job (adjust based on memory/time)
    'batch_size' => env('PAYROLL_BATCH_SIZE', 100),

    // Timeout (seconds) for each batch job – must be < cron/system limit (60s on cPanel)
    'batch_timeout' => env('PAYROLL_BATCH_TIMEOUT', 60),

    // Number of retry attempts for a failed batch job (1 = no retry)
    'batch_tries' => env('PAYROLL_BATCH_TRIES', 1),

    // Finalisation delay per batch (seconds) – used to estimate when all batches are done
    'finalization_delay_per_batch' => env('PAYROLL_FINALIZATION_DELAY_PER_BATCH', 5),

    // Additional buffer time for finalisation (seconds)
    'finalization_buffer' => env('PAYROLL_FINALIZATION_BUFFER', 30),


    /*
    |--------------------------------------------------------------------------
    | Attendance Integration
    |--------------------------------------------------------------------------
    |
    | Enable this to pull attendance records (clock events, sessions) for
    | employees with 'salaried_daily' and 'hourly' pay types.
    | If disabled, all employees are treated as 'salaried_full'.
    |
    */
    'attendance_integration' => [
        'enabled' => env('PAYROLL_ATTENDANCE_INTEGRATION_ENABLED', true),
    ],


];
