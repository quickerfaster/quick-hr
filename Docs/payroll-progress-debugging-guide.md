# Local Shared-Hosting Simulation Guide

This guide helps you simulate the shared cPanel hosting environment locally to debug the payroll progress UI. On shared hosting, the queue worker runs via cron (not as a persistent process), causing a 0-60s delay between job dispatch and processing. The progress UI must handle this delay gracefully.

## Section 1: Setup Local Environment to Mimic Shared Hosting

### 1. Stop All Persistent Queue Workers

First, kill any running `queue:work` or `queue:listen` processes:

```bash
ps aux | grep "queue:work"
# Kill any lingering processes
kill -9 [PID]
```

Or use `pkill`:

```bash
pkill -f "artisan queue:work"
```

### 2. Clear the Queue

```bash
php artisan queue:clear database
```

### 3. Set Queue Driver to Database

In [`.env`](.env), set:

```
QUEUE_CONNECTION=database
```

> **Note:** The default in [`config/queue.php`](config/queue.php:16) is `sync`. The `database` connection is configured at [`config/queue.php`](config/queue.php:37-46) with `retry_after` set to 15000 (4h 10min) to accommodate the longest-running payroll job.

### 4. Verify the Jobs Table Exists

```bash
php artisan migrate
```

If you need to create the jobs table explicitly:

```bash
php artisan queue:table
php artisan migrate
```

### 5. Create a Test Payroll Run

- Log into the application
- Navigate to **Payroll → Run Payroll**
- Select a multi-company run (or single company with employees)
- Set the period
- Complete Steps 1-2 (Company Selection, Adjustments)
- Click "Next" to go to Step 3 (Review & Preview)
- The progress card should appear

### 6. Inspect the Jobs Table

```bash
# See all queued jobs
php artisan tinker
> DB::table('jobs')->get();
> DB::table('jobs')->count();
```

### 7. Run the Worker Manually (Simulating Cron)

```bash
# Run exactly like the cron job does
php artisan queue:work --max-time=55 --timeout=60 --sleep=3 --tries=3
```

Or use the existing [`worker.sh`](worker.sh) script:

```bash
./worker.sh
```

> **How it works on production:** The cPanel cron runs [`worker.sh`](worker.sh) every minute. The script uses `flock` to prevent overlapping workers, then runs `queue:work` with `--max-time=55` (55 seconds, leaving 5 seconds before the next cron tick). This means there can be up to a 60-second delay between when a job is dispatched and when it starts processing.

### 8. Monitor Progress in Real-Time

Open a second terminal and run:

```bash
# Watch the jobs table
watch -n 1 'php artisan tinker --execute="echo DB::table(\"jobs\")->count();"'

# Watch the payroll_run_progress table
watch -n 1 'php artisan tinker --execute="print_r(DB::table(\"payroll_run_progress\")->get()->toArray());"'

# Watch the payroll_runs table
watch -n 1 'php artisan tinker --execute="echo DB::table(\"payroll_runs\")->latest()->first()->calculation_status;"'
```

### 9. Tail the Logs

```bash
tail -f storage/logs/laravel.log | grep -i "payroll\|ProcessPayroll\|ProcessEmployee"
```

### 10. Simulate the Cron Delay

After dispatching the job (clicking Next), **WAIT 30-60 seconds** before running the worker. This mimics the cron delay.

### 11. Test with Sync Driver (Fallback)

```bash
QUEUE_CONNECTION=sync
```

Then click through the wizard again. The page should hang for ~15s then show payslips.

### 12. Verify the Fixes

After running the worker:

- Check `payroll_runs.total_employees > 0`
- Check `payroll_runs.processed_employees > 0`
- Check [`payroll_run_progress`](app/Modules/Hr/Models/PayrollRunProgress.php) table has a record with `total_employees > 0`
- The UI should show incremental progress (if you refresh during processing)
- Final state should show payslips

---

## Section 2: Debugging Checklist

| Step | Command/Action | Expected Result |
|------|---------------|-----------------|
| Verify queue driver | `grep QUEUE_CONNECTION .env` | `QUEUE_CONNECTION=database` |
| Check jobs table exists | `php artisan tinker --execute="echo Schema::hasTable('jobs');"` | `1` (true) |
| Check progress table exists | `php artisan tinker --execute="echo Schema::hasTable('payroll_run_progress');"` | `1` (true) |
| Dispatch a job | Click "Next" in wizard | Job appears in `jobs` table |
| Verify job dispatch | `php artisan tinker --execute="echo DB::table('jobs')->count();"` | `> 0` |
| Run worker | `php artisan queue:work --max-time=55 --timeout=60 --sleep=3 --tries=3` | Job processes, progress record created |
| Check progress record | `php artisan tinker --execute="print_r(DB::table('payroll_run_progress')->get()->toArray());"` | Record with `total_employees > 0` |
| Check payroll_runs sync | `php artisan tinker --execute="\$r = DB::table('payroll_runs')->latest()->first(); echo 'total='.\$r->total_employees.' processed='.\$r->processed_employees;"` | Both > 0 |
| Verify payslips | `php artisan tinker --execute="echo DB::table('payroll_payslips')->where('payroll_run_id', X)->count();"` | > 0 |
| Check UI polling | Open browser devtools Network tab | See XHR requests every 2s to Livewire endpoint |
| Check progress bar | Watch the UI | Progress bar updates from 0% to 100% |

---

## Section 3: Common Issues & Troubleshooting

### Issue: "Job is dispatched but never processed"

- Check: `php artisan queue:failed` — any failed jobs?
- Check: `storage/logs/laravel.log` for errors
- Check: `jobs` table has `attempts` column < 3
- Fix: Run `php artisan queue:retry all` then `php artisan queue:work`

### Issue: "total_employees still 0 after job runs"

- Check: Employee query returns employees

  ```bash
  php artisan tinker
  > $run = \App\Modules\Hr\Models\PayrollRun::find(X);
  > $positions = \App\Modules\Hr\Models\EmployeePosition::withoutCompanyScope()
  >     ->where('employment_status', 'Active')
  >     ->whereHas('employee', fn($q) => $q->withoutCompanyScope()->whereNull('deleted_at'))
  >     ->count();
  > echo $positions;
  ```

- If 0: Check that employees exist with `employment_status = 'Active'` and `deleted_at IS NULL`

### Issue: "Progress bar shows 0% even after job runs"

- Check: [`payroll_runs`](app/Modules/Hr/Models/PayrollRun.php) `total_employees` is synced
- Check: UI polling is hitting the correct endpoint (check Network tab)
- Check: [`PayrollRunProgress`](app/Modules/Hr/Models/PayrollRunProgress.php) record has correct `total_employees`

### Issue: "Multiple jobs dispatched for same run"

- This is now prevented by the jobs table check in `loadPreviewData()`
- If still happening: Check that `DB::table('jobs')->where('payload', 'like', '%ProcessPayrollRun%')` query works correctly

### Issue: "Worker exits immediately without processing"

- Check the worker log: `cat storage/logs/worker.log`
- If you see `"Another worker is running. Exiting."`: A previous worker is still holding the lock. Run `rm -f storage/framework/queue-worker.lock` to force-release it
- If no jobs are found: `queue:work` exits when the queue is empty (by design for cron-based workers)

---

## Section 4: Setting Up the Cron Simulation Script

The project already has a [`worker.sh`](worker.sh) script in the root. Here's how it works:

```bash
#!/bin/bash
# Simulates the cPanel cron worker
# Usage: ./worker.sh

PROJECT_DIR="/home/quickerf/quick_hr"
LOCKFILE="$PROJECT_DIR/storage/framework/queue-worker.lock"
PHP_BIN="/usr/local/bin/php"
ARTISAN="$PROJECT_DIR/artisan"
LOG="$PROJECT_DIR/storage/logs/queue-worker.log"

log() { echo "$(date '+%Y-%m-%d %H:%M:%S') - $*" >> "$LOG"; }

(
    flock -w 2 -n 200 || { log "Another worker is running. Exiting."; exit 0; }
    log "Starting queue worker (max 55 seconds)"
    cd "$PROJECT_DIR"
    $PHP_BIN "$ARTISAN" queue:work --max-time=55 --timeout=60 --sleep=3 --tries=3 --quiet
    log "Worker finished (time limit reached or no jobs)"
) 200>"$LOCKFILE"
```

The script is already executable. Key points:

- **`flock`** prevents overlapping workers (critical for cron-based setups)
- **`--max-time=55`** ensures the worker stops before the next cron tick (at 60 seconds)
- **`--timeout=60`** is the per-job timeout
- **`--sleep=3`** means the worker polls for new jobs every 3 seconds
- **`--tries=3`** limits retries to 3 attempts before marking as failed

### To simulate what happens on shared hosting:

1. Dispatch the job (click "Next" in wizard)
2. Wait 30 seconds (simulating cron delay)
3. Run `./worker.sh`
4. Observe the progress update in the UI

### To test with cron-like frequency:

```bash
# Run worker.sh every minute (like cPanel cron)
while true; do
    ./worker.sh
    sleep 60
done
```

---

## Section 5: Full End-to-End Test Script

```bash
#!/bin/bash
# test-payroll-progress.sh - Full end-to-end test

echo "=== Payroll Progress Debugging Test ==="

# 1. Setup
echo "1. Clearing queue..."
php artisan queue:clear database

echo "2. Resetting test payroll run status..."
php artisan tinker --execute="
    \$r = App\Modules\Hr\Models\PayrollRun::latest()->first();
    if (\$r) {
        \$r->update(['calculation_status' => 'pending', 'total_employees' => 0, 'processed_employees' => 0]);
        echo 'Reset run #' . \$r->id;
    }
"

echo "3. Clearing progress table..."
php artisan tinker --execute="DB::table('payroll_run_progress')->truncate();"

echo "4. Current jobs count:"
php artisan tinker --execute="echo DB::table('jobs')->count();"

echo ""
echo "=== READY ==="
echo "Now:"
echo "1. Open the app in browser, navigate to payroll wizard step 3"
echo "2. The progress card should show 'queued' message"
echo "3. Check jobs table: php artisan tinker --execute=\"echo DB::table('jobs')->count();\""
echo "4. Run worker: ./worker.sh"
echo "5. Watch the UI update"
echo "6. Verify: php artisan tinker --execute=\"print_r(DB::table('payroll_run_progress')->first());\""
```

---

## Key Files Reference

| File | Purpose |
|------|---------|
| [`config/queue.php`](config/queue.php) | Queue configuration (`database` driver, `retry_after=15000`) |
| [`.env`](.env) | Set `QUEUE_CONNECTION=database` |
| [`worker.sh`](worker.sh) | Production cron worker script with `flock` locking |
| [`app/Modules/Hr/Jobs/Payrolls/ProcessPayrollRun.php`](app/Modules/Hr/Jobs/Payrolls/ProcessPayrollRun.php) | The queued job that computes payroll |
| [`app/Modules/Hr/Models/PayrollRun.php`](app/Modules/Hr/Models/PayrollRun.php) | Payroll run model (`total_employees`, `processed_employees`, `calculation_status`) |
| [`app/Modules/Hr/Models/PayrollRunProgress.php`](app/Modules/Hr/Models/PayrollRunProgress.php) | Real-time progress tracking model |
| [`app/Modules/Hr/Http/Livewire/Payroll/PayrollWizardPreview.php`](app/Modules/Hr/Http/Livewire/Payroll/PayrollWizardPreview.php) | Livewire component for step 3 (Review & Preview) |
| [`app/Modules/Hr/Resources/views/livewire/payroll/wizard-preview.blade.php`](app/Modules/Hr/Resources/views/livewire/payroll/wizard-preview.blade.php) | Blade view with progress card UI |
| [`storage/logs/laravel.log`](storage/logs/laravel.log) | Application logs |
| [`storage/logs/worker.log`](storage/logs/worker.log) | Worker-specific logs (from `worker.sh`) |
