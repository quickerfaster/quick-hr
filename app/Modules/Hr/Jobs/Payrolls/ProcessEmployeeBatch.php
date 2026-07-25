<?php

namespace App\Modules\Hr\Jobs\Payrolls;

use App\Modules\Hr\Models\PayrollRun;
use App\Modules\Hr\Models\EmployeePosition;
use App\Modules\Hr\Models\PayrollRunProgress;
use App\Modules\Hr\Services\Payroll\PayrollCalculator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessEmployeeBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout;
    public $tries;

    protected int $payrollRunId;
    protected array $employeeIds;

    public function __construct(int $payrollRunId, array $employeeIds)
    {
        $this->payrollRunId = $payrollRunId;
        $this->employeeIds = $employeeIds;
        $this->timeout = config('quick_hr_payroll.batch_timeout', 60);
        $this->tries = config('quick_hr_payroll.batch_tries', 1);
    }

    public function handle(PayrollCalculator $calculator): void
    {
        $run = PayrollRun::withoutCompanyScope()->find($this->payrollRunId);
        if (!$run) {
            Log::error("Payroll run #{$this->payrollRunId} not found in batch job.");
            return;
        }

        // Get employee positions for these employee IDs (with their relationships)
        $positions = EmployeePosition::withoutCompanyScope()
            ->whereIn('employee_id', $this->employeeIds)
            ->where('employment_status', 'Active')
            ->with([
                'employee' => function ($q) {
                    $q->withoutCompanyScope();
                },
                'location',
                'employee.employeeProfile',
                'employee.user',
            ])
            ->get();

        if ($positions->isEmpty()) {
            Log::info("Batch job: No active positions found for employee IDs: " . implode(',', $this->employeeIds));
            return;
        }

        // Process each employee inside its own transaction to keep it atomic
        foreach ($positions as $position) {
            DB::transaction(function () use ($calculator, $position, $run) {
                // Ensure the calculator uses the correct run
                $calculator->setRun($run);
                $calculator->calculateForEmployee($position);
                // Set company_id on the payslip (if multi-company)
                // The calculator already creates the payslip, but we may need to update its company_id.
                // We'll handle that in the calculator.
            });
        }

        // Increment processed count by the number of employees in this batch
        $count = $positions->count();
PayrollRunProgress::withoutCompanyScope()
    ->where('payroll_run_id', $this->payrollRunId)
    ->increment('processed_employees', $count);

        // If all employees are now processed, mark the run as completed immediately
        // so the preview UI shows payslips without waiting for FinalizePayrollRun's delay.
        $progress = PayrollRunProgress::withoutCompanyScope()
            ->where('payroll_run_id', $this->payrollRunId)
            ->first();

        if ($progress && $progress->processed_employees >= $progress->total_employees) {
            $run = PayrollRun::withoutCompanyScope()->find($this->payrollRunId);
            if ($run && $run->calculation_status !== 'completed') {
                $run->update(['calculation_status' => 'completed']);
            }
        }

        Log::info("Batch job processed {$count} employees for run #{$this->payrollRunId}");
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Employee batch failed for run #{$this->payrollRunId}", [
            'employee_ids' => $this->employeeIds,
            'error' => $exception->getMessage(),
        ]);
    }
}
