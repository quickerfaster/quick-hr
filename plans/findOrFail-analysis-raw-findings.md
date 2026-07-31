# `findOrFail()` / `firstOrFail()` / Risky `::find()` — Raw Findings

**Generated:** 2026-07-30  
**Directories scanned:**
- `app/` (recursively)
- `/Users/mac/Projects/Libraries/ui-library/src/Http/` (recursively)
- `app/Modules/System/` (recursively — **no matches found**)
- Blade templates in `app/Modules/Hr/Resources/views/`

**Scan methods used:** `grep -rn` for `findOrFail`, `firstOrFail`, `::find(`, `ModelNotFoundException`

---

## Summary

| Risk Level | Count |
|-----------|-------|
| **HIGH** | 7 |
| **MEDIUM** | 8 |
| **LOW** | 18 |
| **TOTAL (active)** | 33 |

- **0** `ModelNotFoundException` catch blocks exist anywhere in the codebase — meaning every `findOrFail()` / `firstOrFail()` call will result in an **uncaught 404** if the model is not found.
- **1** occurrence is in a **Blade view** (a particularly bad anti-pattern).
- **4** occurrences accept raw route/request parameters with no scoping.

---

## Detailed Table

| # | File | Line | Method | ID Source | Has Company Scope? | Existing Safeguard? | Context Type | Model | Risk |
|---|------|------|--------|-----------|--------------------|--------------------|--------------|-------|------|
| 1 | [`app/Modules/Admin/Services/AuthorizationService.php`](app/Modules/Admin/Services/AuthorizationService.php:399) | 399 | `findOrFail` | `$recordOrId` (function param, passed from various callers) | No | No | Service class | Dynamic `$modelClass` | **HIGH** |
| 2 | [`app/Modules/Hr/Resources/views/attendance-work-sessions.blade.php`](app/Modules/Hr/Resources/views/attendance-work-sessions.blade.php:7) | 7 | `findOrFail` | `request()->get('attendance_id')` (query string) | No | No (line 6 does a first() but could still be null) | Blade template | `Employee` | **HIGH** |
| 3 | [`app/Modules/Hr/Http/Livewire/Payroll/PayrollRunWizard.php`](app/Modules/Hr/Http/Livewire/Payroll/PayrollRunWizard.php:75) | 75 | `findOrFail` | `$payrollRunId` (Livewire `mount()` parameter) | No explicit scope on findOrFail | No try-catch around this specific call | Livewire Component | `PayrollRun` | **MEDIUM** |
| 4 | [`app/Modules/Hr/Http/Livewire/Payroll/PayrollRunDetail.php`](app/Modules/Hr/Http/Livewire/Payroll/PayrollRunDetail.php:41) | 41 | `findOrFail` | `$recordId` (Livewire `mount()` int parameter) | No explicit scope | No | Livewire Component | `PayrollRun` | **MEDIUM** |
| 5 | [`app/Modules/Hr/Http/Controllers/EmployeeProfileController.php`](app/Modules/Hr/Http/Controllers/EmployeeProfileController.php:14) | 14 | `firstOrFail` | `Auth::id()` (authenticated user) | Implicit — scoped to `user_id` | No try-catch (but scoped to auth user) | Controller | `Employee` | **LOW** |
| 6 | [`app/Modules/Hr/Listeners/AttendanceEventListener.php`](app/Modules/Hr/Listeners/AttendanceEventListener.php:74) | 74 | `findOrFail` | `$attendanceId` (from `$params['attendance_id']` in event data) | No explicit scope | `try-catch` wraps the entire `handleRecalculation` block | Listener | `Attendance` | **MEDIUM** |
| 7 | [`app/Modules/Hr/Http/Livewire/Payroll/PayrollRunWizard.php`](app/Modules/Hr/Http/Livewire/Payroll/PayrollRunWizard.php:309) | 309 | `::find` | `$this->payrollRunId` (Livewire property) | No explicit scope | Null check at line 310 (`if ($run)`) | Livewire Component | `PayrollRun` | **LOW** |
| 8 | [`app/Modules/Hr/Http/Livewire/Payroll/PayrollRunWizard.php`](app/Modules/Hr/Http/Livewire/Payroll/PayrollRunWizard.php:356) | 356 | `::find` | `$this->payrollRunId` (Livewire property) | No explicit scope | Null check at line 357 (`if (!$run)`) | Livewire Component | `PayrollRun` | **LOW** |
| 9 | [`app/Modules/Hr/Http/Livewire/Payroll/PayrollRunWizard.php`](app/Modules/Hr/Http/Livewire/Payroll/PayrollRunWizard.php:438) | 438 | `::find` | `$this->pay_schedule_id` (Livewire property) | No explicit scope | Ternary operator with `? ... : null` pattern | Livewire Component (render) | `PaySchedule` | **LOW** |
| 10 | [`app/Modules/Hr/Http/Livewire/Payroll/PayrollWizardAdjustments.php`](app/Modules/Hr/Http/Livewire/Payroll/PayrollWizardAdjustments.php:362) | 362 | `::find` | `session('current_company_id')` | No (but querying company model) | Null-coalescing `?? 'Unknown Company'` BUT accessing `$company->name` directly first — crashes if null | Livewire Component | `Company` | **MEDIUM** |
| 11 | [`app/Modules/Hr/Http/Livewire/Payroll/PayrollWizardAdjustments.php`](app/Modules/Hr/Http/Livewire/Payroll/PayrollWizardAdjustments.php:419) | 419 | `::find` | `$run->company_id` (relationship value) | No (but querying company model) | Null check: `$company ? $company->name : 'Unknown'` | Livewire Component | `Company` | **LOW** |
| 12 | [`app/Modules/Hr/Http/Livewire/Payroll/PayrollWizardPreview.php`](app/Modules/Hr/Http/Livewire/Payroll/PayrollWizardPreview.php:408) | 408 | `::find` | `session('current_company_id')` | No | Null-coalescing `?? 'Unknown Company'` BUT `$company->name` accessed directly first | Livewire Component | `Company` | **MEDIUM** |
| 13 | [`app/Modules/Hr/Http/Livewire/Payroll/PayrollWizardPreview.php`](app/Modules/Hr/Http/Livewire/Payroll/PayrollWizardPreview.php:480) | 480 | `::find` | `$run->company_id` (relationship value) | No | Null check: `$company ? $company->name : 'Unknown'` | Livewire Component | `Company` | **LOW** |
| 14 | [`app/Modules/Hr/Jobs/Payrolls/ProcessEmployeeBatch.php`](app/Modules/Hr/Jobs/Payrolls/ProcessEmployeeBatch.php:104) | 104 | `::find` | `$this->payrollRunId` (job constructor property) | No explicit scope | Null check at line 105 (`if ($run)`) | Job (`failed()` handler) | `PayrollRun` | **LOW** |
| 15 | [`app/Modules/Hr/Jobs/ProcessAttendanceJob.php`](app/Modules/Hr/Jobs/ProcessAttendanceJob.php:27) | 27 | `::find` | `$this->employeeId` (job constructor property) | No | Null check at line 28 (`if (!$employee)`) | Job | `Employee` | **LOW** |
| 16 | [`app/Modules/Hr/Listeners/LeaveRequestEventListener.php`](app/Modules/Hr/Listeners/LeaveRequestEventListener.php:24) | 24 | `::find` | `$leaveRequestId` (from `$event->newRecord["id"]`) | No | Null check at line 25 (`if ($leaveRequest)`) | Listener | `LeaveRequest` | **LOW** |
| 17 | [`app/Modules/Hr/Services/Payroll/PayrollCalculator.php`](app/Modules/Hr/Services/Payroll/PayrollCalculator.php:162) | 162 | `::find` | `$workPatternId` (function parameter, nullable) | No | Null check at line 163 (`if ($workPattern && ...)`) | Service class | `WorkPattern` | **LOW** |
| 18 | [`app/Modules/Hr/Services/Payroll/PayrollCalculator.php`](app/Modules/Hr/Services/Payroll/PayrollCalculator.php:690) | 690 | `::find` | `$companyId` (loop variable) | No | Null-safe operator `?->name` | Service class | `Company` | **LOW** |
| 19 | [`app/Modules/Hr/Services/Payroll/PayrollCalculator.php`](app/Modules/Hr/Services/Payroll/PayrollCalculator.php:754) | 754 | `::find` | `$companyId` (function parameter) | No | `$company->name ?? 'Unknown'` — **BUG: accesses property on null BEFORE ??** | Service class | `Company` | **HIGH** |
| 20 | [`app/Modules/Hr/Models/EmployeePosition.php`](app/Modules/Hr/Models/EmployeePosition.php:402-411) | 402-411 | `::find` | `$old` / `$new` (audit trail values) | No | Wrapped in `optional()` helper | Model (accessor) | `JobTitle`, `Department`, `Employee` | **LOW** |

### ui-library (`/Users/mac/Projects/Libraries/ui-library/src/Http/`)

| # | File | Line | Method | ID Source | Has Company Scope? | Existing Safeguard? | Context Type | Model | Risk |
|---|------|------|--------|-----------|--------------------|--------------------|--------------|-------|------|
| 21 | [`ui-library/.../DataTableDetail.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/DataTables/DataTableDetail.php:68) | 68 | `findOrFail` | `$this->recordId` (Livewire mount property) | No | No | Livewire Component | Dynamic `$modelClass` | **MEDIUM** |
| 22 | [`ui-library/.../DataTableForm.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/DataTables/DataTableForm.php:672) | 672 | `findOrFail` | `$this->recordId` (Livewire property) | No | Inside `DB::transaction()` but no try-catch around findOrFail | Livewire Component | Dynamic `$this->modelClass` | **MEDIUM** |
| 23 | [`ui-library/.../FilterPanel.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/FilterPanel.php:157) | 157 | `firstOrFail` | `$filterId` (function param) | Scoped to `user_id` via `where('user_id', Auth::id())` | No try-catch (but user-scoped) | Livewire Component | `SavedFilter` | **LOW** |
| 24 | [`ui-library/.../FilterPanel.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/FilterPanel.php:196) | 196 | `firstOrFail` | `$this->editingFilterId` (Livewire property) | Scoped to `user_id` via `where('user_id', Auth::id())` | No try-catch (but user-scoped) | Livewire Component | `SavedFilter` | **LOW** |
| 25 | [`ui-library/.../FilterPanel.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/FilterPanel.php:572) | 572 | `firstOrFail` | `$id` (function param) | Scoped to `user_id` OR `is_global` | No try-catch (but scoped) | Livewire Component | `SavedFilter` | **LOW** |
| 26 | [`ui-library/.../WizardForm.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/Wizards/WizardForm.php:305) | 305 | `findOrFail` | `$this->recordId` (Livewire property) | No | Inside `DB::transaction()` but no try-catch around findOrFail | Livewire Component | Dynamic `$this->modelClass` | **MEDIUM** |
| 27 | [`ui-library/.../EmployeeDetail.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/Custom/EmployeeDetail.php:147) | 147 | `findOrFail` | `$this->recordId` (Livewire mount property) | No | No | Livewire Component | `Employee` | **MEDIUM** |
| 28 | [`ui-library/.../ReportViewer.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/Reports/ReportViewer.php:34) | 34 | `firstOrFail` | `$this->savedReportId` (Livewire mount property) | Scoped to `user_id` via `where('user_id', Auth::id())` | No try-catch (but user-scoped) | Livewire Component | `SavedReport` | **LOW** |
| 29 | [`ui-library/.../ReportBuilder.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/Reports/ReportBuilder.php:38) | 38 | `firstOrFail` | `$this->reportId` (Livewire mount property) | Scoped to `user_id` via `where('user_id', Auth::id())` | No try-catch (but user-scoped) | Livewire Component | `SavedReport` | **LOW** |
| 30 | [`ui-library/.../GenericDetailPagePrintController.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Controllers/Prints/GenericDetailPagePrintController.php:33) | 33 | `findOrFail` | `$id` (route parameter) | No | No | Controller | Dynamic `$modelClass` | **HIGH** |
| 31 | [`ui-library/.../ImportController.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Controllers/Imports/ImportController.php:26) | 26 | `findOrFail` | `$id` (route parameter) | No | No | Controller | `Import` | **HIGH** |
| 32 | [`ui-library/.../ExportController.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Controllers/Exports/ExportController.php:217) | 217 | `findOrFail` | `$id` (route parameter) | No | No | Controller | `Export` | **HIGH** |
| 33 | [`ui-library/.../ExportController.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Controllers/Exports/ExportController.php:449) | 449 | `firstOrFail` | `$id` (route parameter) | Scoped to `user_id` via `where('user_id', auth()->id())` | No try-catch (but user-scoped) | Controller | `Export` | **LOW** |

### Commented-out / Inactive (not counted in totals, included for reference)

| File | Line | Method |
|------|------|--------|
| `app/Modules/Hr/Resources/views/my-profile.blade.php` | 3 | `//->firstOrFail()` (commented out) |
| `app/Modules/Hr/Resources/views/my-account.blade.php` | 3 | `//->firstOrFail()` (commented out) |
| `ui-library/.../AccessControlManager.php` | 102 | `//findOrFail($id)` (commented out) |
| `ui-library/.../AccessControlManager.php` | 105 | `//findOrFail($id)` (commented out) |
| `app/Modules/Hr/Http/Controllers/IdCardController.php` | 26 | `//User::find($userId)` (commented out) |

---

## Raw Code Snippets with Context

### #1 — [`app/Modules/Admin/Services/AuthorizationService.php`](app/Modules/Admin/Services/AuthorizationService.php:394-402)

```php
394:     {
395:         if ($recordOrId instanceof Model) {
396:             return $recordOrId;
397:         }
398:         if (is_int($recordOrId) && $modelClass && class_exists($modelClass)) {
399:             return $modelClass::findOrFail($recordOrId);
400:         }
401:         throw new \InvalidArgumentException('Invalid record or ID/class combination.');
402:     }
```

---

### #2 — [`app/Modules/Hr/Resources/views/attendance-work-sessions.blade.php`](app/Modules/Hr/Resources/views/attendance-work-sessions.blade.php:1-15)

```php
 1: <x-layout>
 2: 
 3: 
 4:     @php
 5:         $attensance_id = request()->get('attendance_id') ?? null;
 6:         $employeeId = \App\Modules\Hr\Models\Attendance::where('id', $attensance_id)->first()?->employee_id;
 7:         $employee = \App\Modules\Hr\Models\Employee::findOrFail($employeeId);
 8:         $subPageTitle = 'For ' . $employee->first_name . ' ' . $employee->last_name . ' (' . $employeeId . ')';
 9:     @endphp
10: 
11: 
12: 
13:     <a href="{{ url()->previous() }}" class="btn bg-gradient-secondary btn-sm my-0">
14:         <i class="bi bi-arrow-left"></i> &larr; Go Back
15:     </a>
```

**⚠️ CRITICAL:** `$employeeId` is `null` when no attendance record is found (line 6 uses `->first()?->employee_id` with null-safe). Then `findOrFail(null)` fails silently with a 404 or unexpected behavior.

---

### #3 — [`app/Modules/Hr/Http/Livewire/Payroll/PayrollRunWizard.php`](app/Modules/Hr/Http/Livewire/Payroll/PayrollRunWizard.php:45-76)

```php
45:     public function mount($payrollRunId = null)
46:     {
47:         $wizardId = $this->getWizardId();
48: 
49:         // Validate stored session data
50:         if (session()->has($wizardId)) {
51:             $data = session()->get($wizardId);
            // ... session validation ...
63:         if (session()->has($wizardId)) {
64:             $data = session()->get($wizardId);
65:             $this->currentStep = $data['currentStep'] ?? 1;
            // ... loads from session ...
74:         } elseif ($payrollRunId) {
75:             $run = PayrollRun::findOrFail($payrollRunId);
76:             $this->payrollRunId = $run->id;
```

The `$payrollRunId` comes directly from the Livewire `mount()` parameter (passed from a blade template via `@livewire('payroll-run-wizard', ['payrollRunId' => $id])`).

---

### #4 — [`app/Modules/Hr/Http/Livewire/Payroll/PayrollRunDetail.php`](app/Modules/Hr/Http/Livewire/Payroll/PayrollRunDetail.php:36-43)

```php
36:     public function mount(int $recordId, string $configKey, array $returnParams = []): void
37:     {
38:         $this->recordId = $recordId;
39:         $this->configKey = $configKey;
40:         $this->returnParams = $returnParams;
41:         $this->run = PayrollRun::with(['paySchedule'])->findOrFail($recordId);
42:         $this->tabs = $this->getTabs();
43:     }
```

---

### #5 — [`app/Modules/Hr/Http/Controllers/EmployeeProfileController.php`](app/Modules/Hr/Http/Controllers/EmployeeProfileController.php:11-21)

```php
11:     public function show()
12:     {
13:         // Find employee linked to the logged-in user
14:         $employee = Employee::where('user_id', Auth::id())->firstOrFail();
15:         
16:         $recordId = $employee->id;
17:         $returnParams = []; // no table state needed
18:         
19:         // Reuse the existing show.blade.php view
20:         return view('hr::employees.show', compact('recordId', 'returnParams'));
21:     }
```

**Note:** Scoped to the authenticated user's `user_id`. If no employee profile exists for the user, it throws 404 — which may be the intended behavior.

---

### #6 — [`app/Modules/Hr/Listeners/AttendanceEventListener.php`](app/Modules/Hr/Listeners/AttendanceEventListener.php:69-83)

```php
69:     private function handleRecalculation($attendanceId, $livewireComponent): void
70:     {
71:         DB::beginTransaction();
72: 
73:         try {
74:             $attendance = Attendance::with(['employee'])->findOrFail($attendanceId);
75: 
76:             // Check if attendance is already approved (from YAML condition)
77:             if ($attendance->is_approved) {
78:                 // throw new \Exception('Cannot recalculate hours ...');
79:                 SweetAlertService::showError($livewireComponent, "Error!", 'Cannot recalculate hours ...');
80:             }
81: 
82:             // Capture before state for audit
83:             ...
```

The `$attendanceId` comes from `$params['attendance_id']` in the event data (line 40). The `try-catch` on line 73 wraps the entire block including the `findOrFail`. However, the catch block below (line 132) catches generic `\Exception` and rolls back the transaction — the `ModelNotFoundException` will be caught. **This means the user gets a rollback but no graceful 404 page.**

---

### #7 — [`app/Modules/Hr/Http/Livewire/Payroll/PayrollRunWizard.php`](app/Modules/Hr/Http/Livewire/Payroll/PayrollRunWizard.php:304-318)

```php
304:                 'company_id'        => $runCompanyId,
305:                 'is_multi_company'  => $this->isMultiCompany,
306:             ]);
307:             $this->payrollRunId = $run->id;
308:         } else {
309:             $run = PayrollRun::find($this->payrollRunId);
310:             if ($run) {
311:                 $run->update([...]);
318:             }
```

Guarded by `if ($run)`.

---

### #8 — [`app/Modules/Hr/Http/Livewire/Payroll/PayrollRunWizard.php`](app/Modules/Hr/Http/Livewire/Payroll/PayrollRunWizard.php:354-365)

```php
354: public function finalize()
355: {
356:     $run = PayrollRun::find($this->payrollRunId);
357:     if (!$run) {
358:         session()->forget($this->getWizardId());
359:         $this->redirectRoute('payroll-runs.create', ['error' => 'Payroll run not found.']);
360:         return;
361:     }
362: 
363:     // Update run status
364:     $run->update([
365:         'status' => 'ready_for_review',
```

Well-guarded with early return.

---

### #9 — [`app/Modules/Hr/Http/Livewire/Payroll/PayrollRunWizard.php`](app/Modules/Hr/Http/Livewire/Payroll/PayrollRunWizard.php:433-441)

```php
438:             'paySchedule' => $this->pay_schedule_id ? PaySchedule::find($this->pay_schedule_id) : null,
```

Uses ternary: if `pay_schedule_id` is falsy, returns null instead of calling `find()`.

---

### #10 — [`app/Modules/Hr/Http/Livewire/Payroll/PayrollWizardAdjustments.php`](app/Modules/Hr/Http/Livewire/Payroll/PayrollWizardAdjustments.php:357-364)

```php
357:     {
358:         $companyId = session('current_company_id');
359:         if (!$companyId || $companyId === 0) {
360:             return 'All Companies';
361:         }
362:         $company = \App\Modules\Admin\Models\Company::find($companyId);
363:         return $company->name ?? 'Unknown Company';
364:     }
```

**⚠️ BUG:** `$company->name` is accessed on a potentially null object BEFORE the `??` operator catches it. If `Company::find()` returns null, this throws "Trying to get property 'name' of non-object". Should be `optional($company)->name ?? 'Unknown Company'` or `$company?->name`.

---

### #11 — [`app/Modules/Hr/Http/Livewire/Payroll/PayrollWizardAdjustments.php`](app/Modules/Hr/Http/Livewire/Payroll/PayrollWizardAdjustments.php:414-421)

```php
418:     if (!$run->is_multi_company && $run->company_id) {
419:         $company = Company::find($run->company_id);
420:         $companyName = $company ? $company->name : 'Unknown Company';
421:     }
```

Properly guarded with `$company ?`.

---

### #12 — [`app/Modules/Hr/Http/Livewire/Payroll/PayrollWizardPreview.php`](app/Modules/Hr/Http/Livewire/Payroll/PayrollWizardPreview.php:403-410)

```php
403:     {
404:         $companyId = session('current_company_id');
405:         if (!$companyId || $companyId === 0) {
406:             return 'All Companies';
407:         }
408:         $company = \App\Modules\Admin\Models\Company::find($companyId);
409:         return $company->name ?? 'Unknown Company';
410:     }
```

**⚠️ SAME BUG as #10:** `$company->name` accessed before `??`.

---

### #13 — [`app/Modules/Hr/Http/Livewire/Payroll/PayrollWizardPreview.php`](app/Modules/Hr/Http/Livewire/Payroll/PayrollWizardPreview.php:477-482)

```php
479:     if (!$run->is_multi_company && $run->company_id) {
480:         $company = Company::find($run->company_id);
481:         $companyName = $company ? $company->name : 'Unknown Company';
482:     }
```

Properly guarded.

---

### #14 — [`app/Modules/Hr/Jobs/Payrolls/ProcessEmployeeBatch.php`](app/Modules/Hr/Jobs/Payrolls/ProcessEmployeeBatch.php:99-108)

```php
 99:         Log::error('ProcessEmployeeBatch failed', [
100:             'run_id' => $this->payrollRunId,
101:             'error' => $exception->getMessage(),
102:         ]);
103: 
104:         $run = \App\Modules\Hr\Models\PayrollRun::find($this->payrollRunId);
105:         if ($run) {
106:             $run->update(['calculation_status' => 'failed']);
107:         }
108:     }
```

Guarded. This is inside the job's `failed()` handler.

---

### #15 — [`app/Modules/Hr/Jobs/ProcessAttendanceJob.php`](app/Modules/Hr/Jobs/ProcessAttendanceJob.php:25-33)

```php
25:     public function handle(AttendanceAggregator $aggregator): void
26:     {
27:         $employee = Employee::find($this->employeeId);
28:         if (!$employee) {
29:             \Log::error("ProcessAttendanceJob: Employee {$this->employeeId} not found");
30:             return;
31:         }
32:         $aggregator->recalculateForDay($employee->employee_number, $this->date);
33:     }
```

Well-guarded.

---

### #16 — [`app/Modules/Hr/Listeners/LeaveRequestEventListener.php`](app/Modules/Hr/Listeners/LeaveRequestEventListener.php:22-31)

```php
22:         if (isset($event->newRecord) && isset($event->newRecord["id"])) {
23:             $leaveRequestId = $event->newRecord["id"];
24:             $leaveRequest = LeaveRequest::find($leaveRequestId);
25:             if ($leaveRequest) {
26:                 $this->approveLeave($leaveRequest, $event);
27:             } else {
28:                 \Log::warning('LeaveRequestEventListener: LeaveRequest not found', [
29:                     'leaveRequestId' => $leaveRequestId
30:                 ]);
31:             }
```

Well-guarded with null check and logging.

---

### #17 — [`app/Modules/Hr/Services/Payroll/PayrollCalculator.php`](app/Modules/Hr/Services/Payroll/PayrollCalculator.php:159-171)

```php
159: protected function getWorkdaysInPeriod(Carbon $start, Carbon $end, ?int $workPatternId): int
160: {
161:     if ($workPatternId) {
162:         $workPattern = \App\Modules\Hr\Models\WorkPattern::find($workPatternId);
163:         if ($workPattern && !empty($workPattern->applicable_days)) {
```

Guarded by `if ($workPattern && ...)`.

---

### #18 — [`app/Modules/Hr/Services/Payroll/PayrollCalculator.php`](app/Modules/Hr/Services/Payroll/PayrollCalculator.php:685-694)

```php
688:             $failedCompanies[] = [
689:                 'company_id' => $companyId,
690:                 'company_name' => Company::find($companyId)?->name ?? 'Unknown',
691:                 'status' => 'failed',
692:                 'error' => substr($e->getMessage(), 0, 500),
693:             ];
```

Uses PHP 8 null-safe operator `?->`, well-guarded.

---

### #19 — [`app/Modules/Hr/Services/Payroll/PayrollCalculator.php`](app/Modules/Hr/Services/Payroll/PayrollCalculator.php:749-756)

```php
749:     protected function processCompany(
750:         int $companyId,
751:         Collection $positions,
752:         int $totalCompanies
753:     ): array {
754:         $company = Company::find($companyId);
755:         $companyName = $company->name ?? 'Unknown';
```

**⚠️ BUG:** Same as #10/#12 — `$company->name` is accessed BEFORE the `??` operator. If `Company::find()` returns null, line 755 throws "Trying to get property 'name' of non-object".

---

### #20 — [`app/Modules/Hr/Models/EmployeePosition.php`](app/Modules/Hr/Models/EmployeePosition.php:397-416)

```php
401:             if ($field === 'job_title_id') {
402:                 $old = optional(\App\Modules\Hr\Models\JobTitle::find($old))->title ?? $old;
403:                 $new = optional(\App\Modules\Hr\Models\JobTitle::find($new))->title ?? $new;
404:                 $field = 'Job Title';
405:             } elseif ($field === 'department_id') {
406:                 $old = optional(\App\Modules\Hr\Models\Department::find($old))->name ?? $old;
407:                 $new = optional(\App\Modules\Hr\Models\Department::find($new))->name ?? $new;
408:                 $field = 'Department';
409:             } elseif ($field === 'manager_id') {
410:                 $old = optional(\App\Modules\Hr\Models\Employee::find($old))->full_name ?? $old;
411:                 $new = optional(\App\Modules\Hr\Models\Employee::find($new))->full_name ?? $new;
```

All wrapped in `optional()`, well-guarded.

---

### #21 — [`ui-library/.../DataTableDetail.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/DataTables/DataTableDetail.php:64-69)

```php
64:     protected function loadRecord(): void
65:     {
66:         $modelClass = $this->getConfigResolver()->getModel();
67:         $relations = array_keys($this->getConfigResolver()->getRelations());
68:         $this->record = $modelClass::with($relations)->findOrFail($this->recordId);
69:     }
```

---

### #22 — [`ui-library/.../DataTableForm.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/DataTables/DataTableForm.php:670-673)

```php
670:         DB::transaction(function () {
671:             $record = $this->isEditMode
672:                 ? $this->modelClass::findOrFail($this->recordId)
673:                 : new $this->modelClass();
```

---

### #23 — [`ui-library/.../FilterPanel.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/FilterPanel.php:152-162)

```php
152:     public function showSaveFilterModal(?int $filterId = null): void
153:     {
154:         if ($filterId) {
155:             $filter = SavedFilter::where('id', $filterId)
156:                 ->where('user_id', Auth::id())
157:                 ->firstOrFail();
158:             $this->filterName = $filter->name;
```

---

### #24 — [`ui-library/.../FilterPanel.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/FilterPanel.php:191-197)

```php
193:         if ($this->editingFilterId) {
194:             $filter = SavedFilter::where('id', $this->editingFilterId)
195:                 ->where('user_id', Auth::id())
196:                 ->firstOrFail();
197:             $filter->update([...]);
```

---

### #25 — [`ui-library/.../FilterPanel.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/FilterPanel.php:567-576)

```php
567:         $saved = SavedFilter::where('id', $id)
568:             ->where(function ($q) {
569:                 $q->where('user_id', Auth::id())
570:                     ->orWhere('is_global', true);
571:             })
572:             ->firstOrFail();
```

---

### #26 — [`ui-library/.../WizardForm.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/Wizards/WizardForm.php:300-308)

```php
300:             return;
301:         }
302: 
303:         DB::transaction(function () {
304:             if ($this->isEditMode) {
305:                 $record = $this->modelClass::findOrFail($this->recordId);
306:             } else {
307:                 $record = new $this->modelClass();
308:             }
```

---

### #27 — [`ui-library/.../EmployeeDetail.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/Custom/EmployeeDetail.php:142-147)

```php
142:             'employeePosition.location',
143:             'employeePosition.shift',
144:             'employeePosition.attendancePolicy',
145:             'jobHistory',
146:             'employeeWorkPatterns.workPattern',
147:         ])->findOrFail($this->recordId);
```

This is on an Employee query builder chain — `$this->recordId` comes from Livewire mount.

---

### #28 — [`ui-library/.../ReportViewer.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/Reports/ReportViewer.php:30-34)

```php
30:         if ($this->savedReportId) {
31:             // Load user-saved report from database
32:             $saved = SavedReport::where('id', $this->savedReportId)
33:                 ->where('user_id', Auth::id())
34:                 ->firstOrFail();
```

---

### #29 — [`ui-library/.../ReportBuilder.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/Reports/ReportBuilder.php:34-38)

```php
34:     if ($this->reportId) {
35:         // Load the saved user report
36:         $saved = SavedReport::where('id', $this->reportId)
37:             ->where('user_id', Auth::id())
38:             ->firstOrFail();
```

---

### #30 — [`ui-library/.../GenericDetailPagePrintController.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Controllers/Prints/GenericDetailPagePrintController.php:29-33)

```php
29:     public function show($configKey, $id)
30:     {
31:         $resolver = app(ConfigResolver::class, ['configKey' => $configKey]);
32:         $modelClass = $resolver->getModel();
33:         $record = $modelClass::findOrFail($id);
```

**⚠️ HIGH RISK:** `$id` is a raw route parameter. Dynamic `$modelClass`. No scoping, no try-catch.

---

### #31 — [`ui-library/.../ImportController.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Controllers/Imports/ImportController.php:24-28)

```php
24:     public function status($id)
25:     {
26:         $import = Import::findOrFail($id);
27:         $completedChunks = ImportChunk::where('import_id', $import->id)->whereIn('status', ['completed', 'failed'])->count();
28:         $totalChunks = $import->total_chunks ?? 0;
```

**⚠️ HIGH RISK:** Raw route parameter `$id`. No company/user scoping, no try-catch. Returns JSON — a `ModelNotFoundException` here will produce an HTML 404 page instead of JSON error response.

---

### #32 — [`ui-library/.../ExportController.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Controllers/Exports/ExportController.php:215-220)

```php
215:     public function exportStatus($id)
216:     {
217:         $export = Export::findOrFail($id);
218:         $fileUrl = $export->status === 'completed' && $export->download_token
219:             ? route('export.download', ['token' => $export->download_token])
220:             : null;
```

**⚠️ HIGH RISK:** Raw route parameter `$id`. No scoping, no try-catch. JSON endpoint that returns HTML 404 on failure.

---

### #33 — [`ui-library/.../ExportController.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Controllers/Exports/ExportController.php:447-449)

```php
447: public function cancelExport($id)
448: {
449:     $export = Export::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
```

User-scoped. **LOW risk.**

---

## HIGH-RISK Items — Prioritized Fix List

| Priority | # | File | Issue |
|----------|---|------|-------|
| 🔴 P0 | 2 | `attendance-work-sessions.blade.php:7` | `findOrFail(null)` when attendance_id is invalid/absent. Plus, this is in a Blade view — move to controller. |
| 🔴 P0 | 30 | `GenericDetailPagePrintController.php:33` | Raw route param + dynamic model, no scoping, no try-catch. |
| 🔴 P0 | 31 | `ImportController.php:26` | Raw route param, JSON endpoint — will return HTML 404. |
| 🔴 P0 | 32 | `ExportController.php:217` | Raw route param, JSON endpoint — will return HTML 404. |
| 🟠 P1 | 1 | `AuthorizationService.php:399` | Dynamic model + integer ID, but the ID comes from various callers — need to audit all callers. |
| 🟠 P1 | 19 | `PayrollCalculator.php:754` | `$company->name ?? 'Unknown'` — null-property-access bug. |
| 🟡 P2 | 10 | `PayrollWizardAdjustments.php:362` | Same null-property-access pattern as #19 (in `getCompanyName()` helper). |
| 🟡 P2 | 12 | `PayrollWizardPreview.php:408` | Same null-property-access pattern as #19 (duplicate code with #10). |

---

## MEDIUM-RISK Items

| # | File | Notes |
|---|------|-------|
| 3 | `PayrollRunWizard.php:75` | Livewire mount param; consider wrapping in try-catch for graceful error |
| 4 | `PayrollRunDetail.php:41` | Same pattern as #3 |
| 6 | `AttendanceEventListener.php:74` | Has try-catch but `\Exception` is too broad; consider catching `ModelNotFoundException` specifically |
| 10 | `PayrollWizardAdjustments.php:362` | Also has the null-property-access bug (listed above) |
| 12 | `PayrollWizardPreview.php:408` | Also has the null-property-access bug (listed above) |
| 21 | `DataTableDetail.php:68` | Generic ui-library component — fix here fixes all detail pages |
| 22 | `DataTableForm.php:672` | Generic ui-library component — fix here fixes all forms |
| 26 | `WizardForm.php:305` | Generic ui-library component |
| 27 | `EmployeeDetail.php:147` | Employee-specific, but same ui-library pattern |

---

## Notes

1. **No `ModelNotFoundException` catch blocks exist anywhere** in `app/` or the ui-library. Every `findOrFail`/`firstOrFail` will propagate to Laravel's default exception handler, producing a 404 page (or HTML-in-JSON for API endpoints).

2. **Three occurrences** (#10, #12, #19) share the same anti-pattern: `$model->property ?? 'default'` where `$model` could be null from `::find()`. In PHP, `$null->property` throws an error **before** `??` evaluates. Use `$model?->property ?? 'default'` (PHP 8) or `optional($model)->property ?? 'default'` (Laravel).

3. The ui-library has **generic** `findOrFail` calls in core components (`DataTableDetail`, `DataTableForm`, `WizardForm`). Fixing these will protect **all** modules that use these components.

4. The `app/Modules/System/` directory exists but contains **no** `findOrFail`/`firstOrFail` calls.

5. Blade view `attendance-work-sessions.blade.php` (#2) is the worst offender — direct `findOrFail()` in a template with a request parameter. This should be refactored into a controller or Livewire component.
