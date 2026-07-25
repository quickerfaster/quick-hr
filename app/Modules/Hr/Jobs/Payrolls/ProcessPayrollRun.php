<?php

namespace App\Modules\Hr\Jobs\Payrolls;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Modules\Hr\Models\PayrollRun;
use App\Modules\Hr\Models\EmployeePosition;
use App\Modules\Hr\Models\PayrollPayslip;
use App\Modules\Hr\Services\Payroll\PayrollCalculator;

use App\Modules\Hr\Jobs\Payrolls\ProcessEmployeeBatch;
use App\Modules\Hr\Jobs\Payrolls\FinalizePayrollRun;


/**
 * Main dispatcher for payroll processing.
 *
 * Validates the run, retrieves active employees, chunks them,
 * and dispatches ProcessEmployeeBatch jobs plus a FinalizePayrollRun job.
 *
 * Designed for shared hosting: runs fast (< 1 second) and holds no long locks.
 */
class ProcessPayrollRun implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * This job only dispatches child jobs, so it completes quickly.
     * Timeout is set high as a safety net (should never be reached).
     */
    public $timeout = 300; // 5 minutes – more than enough

    /**
     * No retries – if this fails, the run must be manually re-triggered.
     */
    public $tries = 1;

    protected int $payrollRunId;

    public function __construct(PayrollRun $payrollRun)
    {
        $this->payrollRunId = $payrollRun->id;
    }

    /**
     * Execute the job: dispatch batch jobs and finalization.
     */
    public function handle(PayrollCalculator $calculator): void
    {
        // ------------------------------------------------------------
        // 1. Atomic status check with DB lock (no cache lock)
        // ------------------------------------------------------------
        $run = DB::transaction(function () {
            $run = PayrollRun::withoutCompanyScope()
                ->where('id', $this->payrollRunId)
                ->lockForUpdate()
                ->first();

            if (!$run) {
                Log::error("Payroll run #{$this->payrollRunId} not found.");
                return null;
            }

            $terminalStates = ['completed', 'completed_with_errors', 'failed'];
            if (in_array($run->calculation_status, $terminalStates, true)) {
                Log::warning("Payroll run #{$this->payrollRunId} already in terminal state: {$run->calculation_status}. Skipping.");
                return null;
            }

            if ($run->calculation_status === 'processing') {
                Log::warning("Payroll run #{$this->payrollRunId} is already being processed. Skipping.");
                return null;
            }

            // Transition to processing
            $run->update([
                'calculation_status' => 'processing',
                'failed_at' => null,
                'failure_reason' => null,
            ]);

            return $run;
        });

        if (!$run) {
            return;
        }

        // ------------------------------------------------------------
        // 2. Ensure multi-company defaults (backward compatibility)
        // ------------------------------------------------------------
        $this->ensureMultiCompanyDefaults($run);

        Log::info("Starting payroll calculation for run #{$run->id}", [
            'is_multi_company' => $run->is_multi_company,
            'pay_schedule_id' => $run->pay_schedule_id,
            'period' => "{$run->period_start->format('Y-m-d')} to {$run->period_end->format('Y-m-d')}",
        ]);

        // ------------------------------------------------------------
        // 3. Determine which employees to process (unscoped)
        // ------------------------------------------------------------
        $employeeQuery = EmployeePosition::withoutCompanyScope()
            ->where('employment_status', 'Active')
            ->whereHas('employee', function ($q) {
                $q->withoutCompanyScope();
            });

        if (!$run->is_multi_company) {
            $employeeQuery->where('pay_schedule_id', $run->pay_schedule_id);
            if ($run->company_id) {
                $employeeQuery->whereHas('employee', function ($q) use ($run) {
                    $q->where('company_id', $run->company_id);
                });
            }
        }

        $employeeIds = $employeeQuery->pluck('employee_id')->toArray();
        $total = count($employeeIds);

        if ($total === 0) {
            Log::warning("Payroll run #{$run->id}: No active employees found.");
            $run->update([
                'calculation_status' => 'failed',
                'failure_reason' => 'No active employees found.',
            ]);
            return;
        }

        // ------------------------------------------------------------
        // 4. Delete old payslips
        // ------------------------------------------------------------
        DB::transaction(function () use ($run) {
            PayrollPayslip::withoutCompanyScope()->where('payroll_run_id', $run->id)->delete();
        });

        // ------------------------------------------------------------
        // 5. Create/update progress record (bypass company scope)
        // ------------------------------------------------------------
        \App\Modules\Hr\Models\PayrollRunProgress::withoutCompanyScope()
            ->updateOrCreate(
                ['payroll_run_id' => $run->id],
                [
                    'total_employees' => $total,
                    'processed_employees' => 0,
                    'status' => 'processing',
                ]
            );

        // ------------------------------------------------------------
        // 6. Chunk and dispatch batch jobs
        // ------------------------------------------------------------
        $batchSize = config('quick_hr_payroll.batch_size', 100);
        $chunks = array_chunk($employeeIds, $batchSize);
        foreach ($chunks as $chunk) {
            ProcessEmployeeBatch::dispatch($run->id, $chunk);
        }

        // ------------------------------------------------------------
        // 7. Dispatch finalization job with a delay
        // ------------------------------------------------------------
        $delayPerBatch = config('quick_hr_payroll.finalization_delay_per_batch', 5);
        $buffer = config('quick_hr_payroll.finalization_buffer', 30);
        $delaySeconds = (count($chunks) * $delayPerBatch) + $buffer;
        FinalizePayrollRun::dispatch($run->id)->delay(now()->addSeconds($delaySeconds));

        Log::info("Dispatched " . count($chunks) . " batch jobs for run #{$run->id}, finalization scheduled in {$delaySeconds}s.");
    }

    /**
     * Handle a job failure (called when tries are exhausted).
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical("Payroll dispatcher permanently failed for run #{$this->payrollRunId}", [
            'error' => $exception->getMessage(),
            'error_class' => get_class($exception),
        ]);

        PayrollRun::withoutCompanyScope()->where('id', $this->payrollRunId)
            ->where('calculation_status', '!=', 'failed')
            ->update([
                'calculation_status' => 'failed',
                'failed_at' => now(),
                'failure_reason' => 'Dispatcher failed: ' . substr($exception->getMessage(), 0, 400),
            ]);
    }

    /**
     * Ensure the payroll run has is_multi_company and per_company_summaries.
     */
    protected function ensureMultiCompanyDefaults(PayrollRun $run): void
    {
        $needsUpdate = false;
        $updates = [];

        if (!isset($run->is_multi_company)) {
            $updates['is_multi_company'] = false;
            $needsUpdate = true;
        }

        if (empty($run->per_company_summaries)) {
            $updates['per_company_summaries'] = json_encode([
                'companies' => [],
                'failed_companies' => [],
            ]);
            $needsUpdate = true;
        }

        if ($needsUpdate) {
            $run->update($updates);
            Log::info("Payroll run #{$run->id}: Set multi-company defaults.", $updates);
        }
    }
}
