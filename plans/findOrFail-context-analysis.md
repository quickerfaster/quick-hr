# `findOrFail()` / `firstOrFail()` / Risky `::find()` — Deep Context Analysis

**Generated:** 2026-07-30
**Based on:** `plans/findOrFail-analysis-raw-findings.md`
**Scope:** HIGH and MEDIUM risk occurrences only (LOW risk items excluded from detailed analysis)

---

## Shared Context: The Company Scoping Mechanism

### How [`CompanyScope`](app/Modules/Admin/Scopes/CompanyScope.php:10) Works

The project uses a **session-based global scope** implemented via the [`HasCompanyScope`](app/Modules/Admin/Traits/HasCompanyScope.php:26) trait:

```php
// CompanyScope::apply()
$companyId = Session::get('current_company_id');
if ($companyId) {
    $table = $model->getTable();
    $builder->where("{$table}.company_id", $companyId);
}
```

**Critical behavior:**
- When `session('current_company_id')` is **set and non-zero**: queries are automatically filtered to that company
- When `session('current_company_id')` is **null or 0**: **NO filtering** — all records are visible
- This means a user with `current_company_id = 0` (the "All Companies" super-admin mode) can see records from **any** company
- **Most Hr models** (`PayrollRun`, `Employee`, `Attendance`, `PaySchedule`, etc.) use `HasCompanyScope`
- The `withoutCompanyScope()` macro is **extensively** used in payroll calculations to bypass session-based filtering

### Key Implication for `findOrFail()` Analysis

`findOrFail()` on a scoped model (e.g., `PayrollRun::findOrFail($id)`) will **only find the record if** it belongs to the company in the current session. An attacker or a user who changes their session `current_company_id` could:

1. Get a **404** for records they should see (their session company doesn't match the record's company)
2. Potentially be **blocked from their own data** if the session value is stale or wrong

---

## HIGH-RISK Items — Detailed Analysis

---

### Item #1: [`AuthorizationService.php`](app/Modules/Admin/Services/AuthorizationService.php:399)

**Current code:**
```php
393:     private function resolveRecord($recordOrId, ?string $modelClass = null): Model
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

**ID Source Trace:**
- `resolveRecord()` is called from [`authorize()`](app/Modules/Admin/Services/AuthorizationService.php:274) which receives `$recordOrId` from the public-facing methods: [`authorizeView()`](app/Modules/Admin/Services/AuthorizationService.php:288), [`authorizeUpdate()`](app/Modules/Admin/Services/AuthorizationService.php:293), [`authorizeEdit()`](app/Modules/Admin/Services/AuthorizationService.php:298), and [`authorizeDelete()`](app/Modules/Admin/Services/AuthorizationService.php:308).
- These public methods are called from **ui-library components** and **module controllers** passing `$this->recordId` (an integer from Livewire `mount()` parameter).
- Example call chain in [`DataTableForm::mount()`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/DataTables/DataTableForm.php:103):
  ```php
  app(AuthorizationService::class)->authorizeUpdate(auth()->user(), $this->recordId, $this->modelClass);
  ```
- `$this->recordId` comes from the blade template as a Livewire mount parameter, which ultimately traces back to a route parameter or a row click in a data table.

**Company Scoping Analysis:**
- No company scoping on the `findOrFail()` call itself — it calls `$modelClass::findOrFail()` directly.
- HOWEVER, if `$modelClass` uses `HasCompanyScope`, the global `CompanyScope` will apply a `WHERE company_id = session('current_company_id')` filter silently.
- This is the **dual-bug**: if the session company is wrong/stale, `findOrFail()` returns 404 even for records the user legitimately owns.

**Existing Safeguards:**
- **None.** No try-catch around the `findOrFail`. No `withoutCompanyScope()`. No null check on the ID.
- The check `is_int($recordOrId)` is weak — it only validates type, not that the ID is positive or exists.

**Risk Scenario:**
1. A super admin switches to "All Companies" mode (`current_company_id = 0`), opens a data table, then switches to a specific company via the company switcher (`current_company_id = 5`)
2. They click "Edit" on a record that was loaded in the "All Companies" view (belongs to company 3)
3. `authorizeUpdate()` → `resolveRecord()` → `$modelClass::findOrFail(recordId)` where CompanyScope filters to company 5
4. The record belongs to company 3 → **404 Not Found** (the raw `ModelNotFoundException`)
5. The user sees a crash/error page with no useful message

Additionally, if someone manually enters a URL with an invalid ID, they get an uncaught 404.

**Recommended Fix:**
```php
private function resolveRecord($recordOrId, ?string $modelClass = null): ?Model
{
    if ($recordOrId instanceof Model) {
        return $recordOrId;
    }
    if (is_int($recordOrId) && $recordOrId > 0 && $modelClass && class_exists($modelClass)) {
        // Use withoutCompanyScope() since this is AuthorizationService — 
        // it should find the record first, THEN check if user is authorized.
        // Company scoping is a data-access concern, not an auth concern.
        $record = $modelClass::withoutCompanyScope()->find($recordOrId);
        if (!$record) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
                "Record {$recordOrId} not found for model {$modelClass}"
            );
        }
        return $record;
    }
    throw new \InvalidArgumentException('Invalid record or ID/class combination.');
}
```

Then in the calling `authorize()` method, catch `ModelNotFoundException` and convert to a 404:
```php
try {
    $record = $this->resolveRecord($recordOrId, $modelClass);
} catch (ModelNotFoundException $e) {
    abort(404, 'Record not found.');
}
```

---

### Item #2: [`attendance-work-sessions.blade.php`](app/Modules/Hr/Resources/views/attendance-work-sessions.blade.php:7)

**Current code:**
```php
 4:     @php
 5:         $attensance_id = request()->get('attendance_id') ?? null;
 6:         $employeeId = \App\Modules\Hr\Models\Attendance::where('id', $attensance_id)->first()?->employee_id;
 7:         $employee = \App\Modules\Hr\Models\Employee::findOrFail($employeeId);
 8:         $subPageTitle = 'For ' . $employee->first_name . ' ' . $employee->last_name . ' (' . $employeeId . ')';
 9:     @endphp
```

**ID Source Trace:**
- `$attensance_id` = `request()->get('attendance_id')` — a **raw query string parameter** from the URL (e.g., `?attendance_id=123`)
- `$employeeId` = result of `Attendance::where('id', $attensance_id)->first()?->employee_id`
  - If attendance_id is missing: `$attensance_id = null` → `where('id', null)` → no match → `first()` returns null → `?->employee_id` = **null**
  - If attendance_id is invalid: same as above
- `$employeeId` is then passed to `Employee::findOrFail(null)` which causes a crash

**Company Scoping Analysis:**
- `Attendance` uses `HasCompanyScope` — the `first()` on line 6 will only find attendance records belonging to `session('current_company_id')`
- `Employee` uses `HasCompanyScope` — the `findOrFail()` will be further filtered by company
- This means even with a valid `attendance_id`, the record may not be found if the session company is wrong

**Existing Safeguards:**
- **None.** No null check on `$employeeId` before `findOrFail()`. No try-catch. No validation.
- This is raw PHP code embedded in a Blade template — the worst possible place for database queries.

**Risk Scenario:**
1. User navigates to `/hr/attendance-work-sessions?attendance_id=99999` (non-existent ID)
2. `Attendance::where('id', 99999)->first()` returns null, `?->employee_id` = null
3. `Employee::findOrFail(null)` → `ModelNotFoundException` → uncaught 404
4. OR: User navigates to `/hr/attendance-work-sessions` (no query param)
5. Same crash path — `null` is passed to `findOrFail()`

**Recommended Fix:**
Move all logic out of the Blade view into a controller:

```php
// In a controller:
public function workSessions(Request $request)
{
    $attendanceId = $request->get('attendance_id');
    
    if (!$attendanceId) {
        abort(404, 'Attendance ID is required.');
    }
    
    $attendance = Attendance::where('id', $attendanceId)->first();
    
    if (!$attendance || !$attendance->employee_id) {
        abort(404, 'Attendance record not found or has no associated employee.');
    }
    
    $employee = Employee::find($attendance->employee_id);
    
    if (!$employee) {
        abort(404, 'Employee not found.');
    }
    
    $subPageTitle = 'For ' . $employee->first_name . ' ' . $employee->last_name . ' (' . $employee->employee_id . ')';
    
    return view('hr::attendance-work-sessions', compact('attendanceId', 'subPageTitle'));
}
```

As an immediate quick-fix in the blade (if controller refactor is not possible):
```php
@php
    $attendance_id = request()->get('attendance_id');
    $employeeId = null;
    $employee = null;
    
    if ($attendance_id) {
        $attendance = \App\Modules\Hr\Models\Attendance::where('id', $attendance_id)->first();
        if ($attendance && $attendance->employee_id) {
            $employee = \App\Modules\Hr\Models\Employee::find($attendance->employee_id);
        }
    }
    
    if (!$employee) {
        abort(404, 'Attendance record or employee not found.');
    }
    
    $subPageTitle = 'For ' . $employee->first_name . ' ' . $employee->last_name . ' (' . $employeeId . ')';
@endphp
```

---

### Item #19: [`PayrollCalculator.php`](app/Modules/Hr/Services/Payroll/PayrollCalculator.php:754-755)

**Current code:**
```php
749:     protected function processCompany(
750:         int $companyId,
751:         Collection $positions,
752:         int $totalCompanies
753:     ): array {
754:         $company = Company::find($companyId);
755:         $companyName = $company->name ?? 'Unknown';
```

**ID Source Trace:**
- `$companyId` comes from [`processMultiCompany()`](app/Modules/Hr/Services/Payroll/PayrollCalculator.php:600-610) which queries:
  ```php
  $companyIds = EmployeePosition::withoutCompanyScope()
      ->where('employment_status', 'Active')
      ->whereHas('employee', function ($q) { $q->withoutCompanyScope(); })
      ->join('employees', ...)
      ->select('employees.company_id')
      ->distinct()
      ->pluck('company_id')
      ->filter() // remove nulls
      ->values();
  ```
- The company IDs are derived from **live database data** (active employees' company assignments)
- However, there is a time gap between fetching `$companyIds` and calling `processCompany()` for each one — if a company is deleted in this window, `::find()` returns null

**Company Scoping Analysis:**
- `Company::find()` does **NOT** use `HasCompanyScope` (the Company model likely does not have it)
- No company scoping applies — `::find()` is a direct PK lookup

**Existing Safeguards:**
- **None — this is a BUG.** `$company->name` is accessed on a potentially null object **before** the `??` operator evaluates.
- In PHP, `$company->name ?? 'Unknown'` when `$company` is null throws: **"Trying to get property 'name' of non-object"**

**Risk Scenario:**
1. A multi-company payroll run starts processing
2. The list of company IDs is fetched — includes company #5
3. While the calculator is processing company #3, an admin deletes company #5
4. The loop reaches company #5 → `Company::find(5)` returns null
5. `$company->name ?? 'Unknown'` → **PHP Error: Trying to get property 'name' of non-object**
6. The entire payroll run crashes with a 500 error

**Recommended Fix:**
```php
$company = Company::find($companyId);
$companyName = $company?->name ?? 'Unknown';     // PHP 8 null-safe operator
// OR:
$companyName = optional($company)->name ?? 'Unknown';   // Laravel helper
```

Additionally, consider adding an early return if company is not found:
```php
$company = Company::find($companyId);
if (!$company) {
    Log::warning("Company #{$companyId} not found during payroll calculation", [
        'run_id' => $this->run->id,
    ]);
    return [
        'company_id' => $companyId,
        'company_name' => 'Unknown Company (deleted)',
        'employee_count' => 0,
        'status' => 'skipped',
    ];
}
$companyName = $company->name;
```

---

### Item #30: [`GenericDetailPagePrintController.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Controllers/Prints/GenericDetailPagePrintController.php:33)

**Current code:**
```php
29:     public function show($configKey, $id)
30:     {
31:         $resolver = app(ConfigResolver::class, ['configKey' => $configKey]);
32:         $modelClass = $resolver->getModel();
33:         $record = $modelClass::findOrFail($id);
```

**ID Source Trace:**
- `$id` is a **raw route parameter** from the URL `/print/{configKey}/{id}` (defined at [`web.php`](/Users/mac/Projects/Libraries/ui-library/src/Routes/web.php:88))
- A user can type **any** value — `/print/employees/99999` or `/print/employees/abc`
- `$configKey` is also a raw route parameter, so an attacker can craft `/print/nonexistent/123`

**Company Scoping Analysis:**
- The `$modelClass` is dynamic — determined by `ConfigResolver` from the `$configKey`
- If the resolved model uses `HasCompanyScope`, the global scope will silently filter
- If it doesn't, no scoping applies
- Either way, no **explicit** company scoping is done

**Existing Safeguards:**
- **None.** No validation, no try-catch, no authorization check, no null check
- The route at [web.php:88](/Users/mac/Projects/Libraries/ui-library/src/Routes/web.php:88) has **no middleware** — it's not even in an `auth` group
- **This means anyone can access this endpoint without being logged in!**

**Risk Scenario:**
1. An unauthenticated user navigates to `/print/employees/1` (valid employee)
2. They get a **print view** of employee data they should not see — massive data leak
3. OR: `/print/employees/99999` → `findOrFail` → uncaught 404 → ugly error page

**Recommended Fix:**
```php
public function show($configKey, $id)
{
    // Validate configKey
    try {
        $resolver = app(ConfigResolver::class, ['configKey' => $configKey]);
        $modelClass = $resolver->getModel();
    } catch (\Exception $e) {
        abort(404, 'Configuration not found.');
    }
    
    // Validate $id is numeric
    if (!is_numeric($id) || $id <= 0) {
        abort(404, 'Invalid record ID.');
    }
    
    $record = $modelClass::find($id);
    
    if (!$record) {
        abort(404, 'Record not found.');
    }
    
    // ADD AUTHORIZATION CHECK
    $this->authorize('view', $record);  // or use AuthorizationService
    
    // ... rest of method
}
```

The route should also be added to an auth middleware group:
```php
Route::middleware(['auth'])->group(function () {
    Route::get('/print/{configKey}/{id}', [GenericDetailPagePrintController::class, 'show'])
        ->name('generic.print');
});
```

---

### Item #31: [`ImportController.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Controllers/Imports/ImportController.php:26)

**Current code:**
```php
24:     public function status($id)
25:     {
26:         $import = Import::findOrFail($id);
27:         $completedChunks = ImportChunk::where('import_id', $import->id)->whereIn('status', ['completed', 'failed'])->count();
28:         $totalChunks = $import->total_chunks ?? 0;
29:         // ... returns JSON response
```

**ID Source Trace:**
- `$id` is a **raw route parameter** from `/import/status/{id}` (defined at [`web.php`](/Users/mac/Projects/Libraries/ui-library/src/Routes/web.php:61))
- The `Import` model is from the `QuickerFaster\UILibrary\Models\Import` namespace — a package model
- This endpoint is polled by JavaScript (AJAX) to check import progress

**Company Scoping Analysis:**
- `Import` model likely does **NOT** use `HasCompanyScope` — it's a ui-library model, not a module model
- No user scoping is applied — any authenticated user can check the status of **any** import by guessing the ID
- Cross-tenant data leak risk: User A can poll the status of User B's import

**Existing Safeguards:**
- **None.** No try-catch, no user scoping, no ownership check
- The route is in the `web` middleware group (line 36-61) but that only provides session/cookie handling, not auth
- The endpoint returns **JSON** — but `ModelNotFoundException` results in an **HTML 404 page**, not a JSON error. JavaScript polling code expects JSON and will break.

**Risk Scenario:**
1. User A starts a bulk import of employees — the JavaScript frontend polls `/import/status/42` every 2 seconds
2. User A deletes the import (or an admin cancels it) — the record is removed from the database
3. The next poll to `/import/status/42` hits `findOrFail(42)` → `ModelNotFoundException`
4. Laravel returns an **HTML 404 page** instead of JSON
5. The JavaScript `response.json()` call throws a parsing error → the UI hangs indefinitely with no error reported

Also exploitable:
1. User B discovers the poll URLs and tries `/import/status/42` (User A's import)
2. They can see User A's import status, row counts, error file URLs
3. Information disclosure across users

**Recommended Fix:**
```php
public function status($id)
{
    // Scope to the authenticated user AND handle missing
    $import = Import::where('id', $id)
        ->where('user_id', auth()->id())
        ->first();
    
    if (!$import) {
        return response()->json([
            'status' => 'not_found',
            'error' => 'Import not found or you do not have access.',
        ], 404);
    }
    
    $completedChunks = ImportChunk::where('import_id', $import->id)
        ->whereIn('status', ['completed', 'failed'])
        ->count();
    $totalChunks = $import->total_chunks ?? 0;
    $errorFileUrl = $import->error_file ? route('import.download-errors', $import) : null;

    return response()->json([
        'status' => $import->status,
        'total_rows' => $import->total_rows,
        'successful_rows' => $import->successful_rows,
        'failed_rows' => $import->failed_rows,
        'completed_chunks' => $completedChunks,
        'total_chunks' => $totalChunks,
        'error_file_url' => $errorFileUrl,
    ]);
}
```

---

### Item #32: [`ExportController.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Controllers/Exports/ExportController.php:217)

**Current code:**
```php
215:     public function exportStatus($id)
216:     {
217:         $export = Export::findOrFail($id);
218:         $fileUrl = $export->status === 'completed' && $export->download_token
219:             ? route('export.download', ['token' => $export->download_token])
220:             : null;
```

**ID Source Trace:**
- `$id` is a **raw route parameter** from `/export/status/{id}` (defined at [`web.php`](/Users/mac/Projects/Libraries/ui-library/src/Routes/web.php:55))
- This is the **exact same pattern** as Item #31 but for exports — polled by JavaScript

**Company Scoping Analysis:**
- `Export` model likely does **NOT** use `HasCompanyScope` — it's a ui-library model
- No user scoping — any authenticated user can poll any export's status
- Cross-tenant data leak: User A can see User B's export file URL and download link

**Existing Safeguards:**
- **None.** Same issues as Item #31 — no user scoping, no try-catch, JSON endpoint returns HTML 404

**Risk Scenario:**
1. Same polling crash scenario as Item #31
2. User A polls `/export/status/10` (User B's export that was just completed)
3. The JSON response includes `file_url` — a direct download link to User B's exported data
4. User A can download User B's data without authorization

**Recommended Fix:**
```php
public function exportStatus($id)
{
    $export = Export::where('id', $id)
        ->where('user_id', auth()->id())
        ->first();
    
    if (!$export) {
        return response()->json([
            'status' => 'not_found',
            'error' => 'Export not found or you do not have access.',
        ], 404);
    }
    
    $fileUrl = $export->status === 'completed' && $export->download_token
        ? route('export.download', ['token' => $export->download_token])
        : null;

    // Calculate chunk progress
    $completedChunks = 0;
    $totalChunks = $export->total_chunks ?? 0;
    if ($totalChunks > 0 && in_array($export->status, ['processing', 'pending', 'completed'])) {
        $completedChunks = ExportChunk::where('export_id', $export->id)->count();
    }

    return response()->json([
        'status' => $export->status,
        'file_url' => $fileUrl,
        'file_size' => $export->file_size,
        'error' => $export->error_message,
        'completed_at' => $export->completed_at,
        'completed_chunks' => $completedChunks,
        'total_chunks' => $totalChunks,
    ]);
}
```

---

## MEDIUM-RISK Items — Detailed Analysis

---

### Item #3: [`PayrollRunWizard.php`](app/Modules/Hr/Http/Livewire/Payroll/PayrollRunWizard.php:75)

**Current code:**
```php
45:     public function mount($payrollRunId = null)
46:     {
47:         $wizardId = $this->getWizardId();
48: 
49:         // Validate stored session data
50:         if (session()->has($wizardId)) {
51:             $data = session()->get($wizardId);
52:             if (isset($data['payrollRunId']) && $data['payrollRunId']) {
53:                 $exists = PayrollRun::withoutCompanyScope()
54:                     ->where('id', $data['payrollRunId'])
55:                     ->exists();
56:                 if (!$exists) {
57:                     session()->forget($wizardId);
58:                     $data = null;
59:                 }
60:             }
61:         }
62: 
63:         if (session()->has($wizardId)) {
64:             // ... loads from session ...
73:             $this->companyId = $data['companyId'] ?? null;
74:         } elseif ($payrollRunId) {
75:             $run = PayrollRun::findOrFail($payrollRunId);
76:             $this->payrollRunId = $run->id;
```

**ID Source Trace:**
- `$payrollRunId` is a **Livewire `mount()` parameter** passed from a blade template via:
  ```blade
  @livewire('payroll-run-wizard', ['payrollRunId' => $id])
  ```
- The `$id` comes from a route parameter or a previous Livewire component's property
- The wizard also stores state in session via `saveToSession()` (identified by `getWizardId()` = `'payroll-wizard-' . auth()->id()`)

**Company Scoping Analysis:**
- `PayrollRun` uses `HasCompanyScope` — `findOrFail()` is scoped by `session('current_company_id')`
- **BUT** the session validation block (lines 52-55) uses `withoutCompanyScope()` — it checks if the run exists regardless of session company
- The `findOrFail()` on line 75 does **NOT** use `withoutCompanyScope()` — it WILL be scoped
- **Inconsistency:** Session-restored wizards bypass company scope; direct `$payrollRunId` mounts do not

**Existing Safeguards:**
- Session validation at lines 52-60: if a stored `payrollRunId` no longer exists in DB, the session is cleared
- Wizard ID is user-specific (`'payroll-wizard-' . auth()->id()`) — prevents cross-user session leaks
- No try-catch around `findOrFail()` on line 75

**Risk Scenario:**
1. Super admin is in "All Companies" mode (`current_company_id = 0`)
2. They navigate to `/hr/payroll-runs/42/edit` where run #42 belongs to company 3
3. With `current_company_id = 0`, CompanyScope applies no filter → `findOrFail(42)` succeeds → wizard loads
4. Admin changes company switcher to company 5 (for another task) **while the wizard page is still open**
5. A Livewire re-render triggers `mount()` again (or the admin returns to the wizard)
6. Now `current_company_id = 5`, CompanyScope filters to company 5, but run #42 belongs to company 3
7. `findOrFail(42)` → **404** — the wizard crashes

**Recommended Fix:**
```php
} elseif ($payrollRunId) {
    $run = PayrollRun::withoutCompanyScope()->find($payrollRunId);
    if (!$run) {
        // Return a graceful error via Livewire flash message or redirect
        session()->flash('error', 'Payroll run not found.');
        $this->redirectRoute('payroll-runs.index');
        return;
    }
    $this->payrollRunId = $run->id;
    // ... rest of initialization
```

---

### Item #4: [`PayrollRunDetail.php`](app/Modules/Hr/Http/Livewire/Payroll/PayrollRunDetail.php:41)

**Current code:**
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

**ID Source Trace:**
- `$recordId` is a **Livewire `mount()` int parameter** passed from a blade template
- The component is rendered from a detail page route (e.g., `/hr/payroll-runs/{id}`)
- The `$recordId` is the route parameter passed through as `int $recordId` in the Livewire mount

**Company Scoping Analysis:**
- `PayrollRun` uses `HasCompanyScope` — `findOrFail()` is filtered by `session('current_company_id')`
- No `withoutCompanyScope()` is used
- This means the user can only view runs belonging to their session's company

**Existing Safeguards:**
- **None.** No try-catch, no null check, no `withoutCompanyScope()`
- The `int` type hint on `$recordId` enforces it's an integer, but doesn't validate it's a valid ID

**Risk Scenario:**
1. User is viewing a payroll run detail page for run #42 (company 3), `current_company_id = 3`
2. They use the company switcher to switch to company 5
3. The page may re-render (Livewire component re-mounts)
4. `PayrollRun::findOrFail(42)` is now scoped to company 5 → not found → **404**
5. User sees an error page instead of a graceful redirect

**Recommended Fix:**
```php
public function mount(int $recordId, string $configKey, array $returnParams = []): void
{
    $this->recordId = $recordId;
    $this->configKey = $configKey;
    $this->returnParams = $returnParams;
    
    $run = PayrollRun::withoutCompanyScope()->with(['paySchedule'])->find($recordId);
    
    if (!$run) {
        // Graceful handling — redirect with error message
        session()->flash('error', 'Payroll run not found.');
        $this->redirectRoute('payroll-runs.index');
        return;
    }
    
    $this->run = $run;
    $this->tabs = $this->getTabs();
}
```

---

### Item #6: [`AttendanceEventListener.php`](app/Modules/Hr/Listeners/AttendanceEventListener.php:74)

**Current code:**
```php
69:     private function handleRecalculation($attendanceId, $livewireComponent): void
70:     {
71:         DB::beginTransaction();
72: 
73:         try {
74:             $attendance = Attendance::with(['employee'])->findOrFail($attendanceId);
75: 
76:             // Check if attendance is already approved
77:             if ($attendance->is_approved) {
78:                 SweetAlertService::showError($livewireComponent, "Error!", 'Cannot recalculate...');
79:             }
...
132:         } catch (\Exception $e) {
133:             DB::rollBack();
134: 
135:             Log::error('Attendance recalculation failed', [
136:                 'attendance_id' => $attendanceId,
137:                 'error' => $e->getMessage(),
138:                 'trace' => $e->getTraceAsString()
139:             ]);
```

**ID Source Trace:**
- `$attendanceId` comes from [`$params['attendance_id']`](app/Modules/Hr/Listeners/AttendanceEventListener.php:40) in the event data
- The event is triggered by the `DataTableFormEvent` system when a form action is submitted
- The `attendance_id` is extracted from a YAML-configured action parameter
- It's essentially a user-triggered action from a data table row

**Company Scoping Analysis:**
- `Attendance` uses `HasCompanyScope` — `findOrFail()` is scoped by session company
- No `withoutCompanyScope()` on this specific call

**Existing Safeguards:**
- There IS a `try-catch` wrapping the findOrFail (line 73)
- The catch block (line 132): catches generic `\Exception`, rolls back transaction, logs error
- **Problem:** `ModelNotFoundException` is caught as a generic `\Exception` — the user gets a rollback but no specific feedback

**Risk Scenario:**
1. User clicks "Recalculate Hours" on an attendance row in a data table
2. Between the time the page loaded and the button click, the attendance record was deleted (by an admin or automation)
3. `findOrFail($attendanceId)` throws `ModelNotFoundException`
4. The catch block catches it, rolls back the transaction, logs an error
5. BUT the SweetAlert success/failure notifications at lines 127 and 140 wrap around the try-catch
6. The user sees **no error message** — the action silently fails with just a DB rollback

**Recommended Fix:**
Add specific handling for ModelNotFoundException:
```php
} catch (\Exception $e) {
    DB::rollBack();
    
    if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
        Log::warning('Attendance recalculation failed: record not found', [
            'attendance_id' => $attendanceId,
        ]);
        SweetAlertService::showError($livewireComponent, "Error!", 'Attendance record not found. It may have been deleted.');
    } else {
        Log::error('Attendance recalculation failed', [
            'attendance_id' => $attendanceId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        SweetAlertService::showError($livewireComponent, "Error!", 'Recalculation failed: ' . $e->getMessage());
    }
}
```

---

### Item #10: [`PayrollWizardAdjustments.php`](app/Modules/Hr/Http/Livewire/Payroll/PayrollWizardAdjustments.php:362-363)

**Current code:**
```php
356:     public function getCurrentCompanyNameProperty(): string
357:     {
358:         $companyId = session('current_company_id');
359:         if (!$companyId || $companyId === 0) {
360:             return 'All Companies';
361:         }
362:         $company = \App\Modules\Admin\Models\Company::find($companyId);
363:         return $company->name ?? 'Unknown Company';
364:     }
```

**ID Source Trace:**
- `$companyId` = `session('current_company_id')` — the company switcher's session value
- This is a **computed Livewire property** (getter) called whenever the template references `$this->currentCompanyName`

**Company Scoping Analysis:**
- `Company::find()` is a direct PK lookup on the `Company` model (not from the Hr module)
- Company model likely does **not** use `HasCompanyScope`

**Existing Safeguards:**
- **BUG:** `$company->name ?? 'Unknown Company'` — if `::find()` returns null, PHP throws **"Trying to get property 'name' of non-object"** before `??` evaluates
- There's a guard for `!$companyId || $companyId === 0` but not for the case where the ID exists but the company has been deleted

**Risk Scenario:**
1. Admin has `current_company_id = 42` in session
2. Another admin deletes company #42
3. The wizard page with adjustments re-renders → `getCurrentCompanyNameProperty()` is called
4. `Company::find(42)` → null
5. `$company->name ?? 'Unknown'` → **PHP Error crash**

**Recommended Fix:**
```php
$company = \App\Modules\Admin\Models\Company::find($companyId);
return $company?->name ?? 'Unknown Company';   // PHP 8 null-safe
// OR:
return optional($company)->name ?? 'Unknown Company';  // Laravel helper
```

---

### Item #12: [`PayrollWizardPreview.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/Wizards/WizardForm.php:305) ... wait, this is from the main app at [app/Modules/Hr/Http/Livewire/Payroll/PayrollWizardPreview.php:408-409]

**Current code:**
```php
402:     public function getCurrentCompanyNameProperty(): string
403:     {
404:         $companyId = session('current_company_id');
405:         if (!$companyId || $companyId === 0) {
406:             return 'All Companies';
407:         }
408:         $company = \App\Modules\Admin\Models\Company::find($companyId);
409:         return $company->name ?? 'Unknown Company';
410:     }
```

**ID Source Trace:**
- **Exact same pattern as Item #10** — copy-pasted code
- `$companyId` = `session('current_company_id')`
- This is in `PayrollWizardPreview` (a different Livewire component but same codebase)

**Company Scoping Analysis:**
- Identical to Item #10

**Existing Safeguards:**
- **Same BUG as Item #10:** `$company->name ?? 'Unknown Company'` crashes on null `$company`

**Risk Scenario:**
- Identical to Item #10

**Recommended Fix:**
- Same as Item #10 — use `$company?->name` or `optional($company)->name`
- **Better approach:** Extract this into a shared helper to prevent code duplication:
  ```php
  // In a trait or base class:
  protected function getCompanyName(int $companyId): string
  {
      $company = \App\Modules\Admin\Models\Company::find($companyId);
      return $company?->name ?? 'Unknown Company';
  }
  ```

---

### Item #21: [`DataTableDetail.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/DataTables/DataTableDetail.php:68)

**Current code:**
```php
25:     public function mount(string $configKey, int $recordId, $inline = false, array $returnParams = [])
26:     {
27:         $this->configKey = $configKey;
28:         $this->recordId = $recordId;
29:         $this->returnParams = $returnParams;
30:         $this->inline = $inline;
31: 
32:         $this->loadConfiguration();
33:         $this->loadRecord();                                            // ← findOrFail HERE
34: 
35:         $modelClass = app(ConfigResolver::class, ['configKey' => $this->configKey])->getModel();
36:         app(AuthorizationService::class)->authorizeView(auth()->user(), $this->recordId, $modelClass);  // ← findOrFail AGAIN
37:     }
```

With [`loadRecord()`](line:64):
```php
64:     protected function loadRecord(): void
65:     {
66:         $modelClass = $this->getConfigResolver()->getModel();
67:         $relations = array_keys($this->getConfigResolver()->getRelations());
68:         $this->record = $modelClass::with($relations)->findOrFail($this->recordId);
69:     }
```

**ID Source Trace:**
- `$recordId` is a **Livewire `mount()` parameter** — an `int` passed from a blade template
- The blade template receives it from a controller that extracted it from a route parameter
- Example: `/hr/employees/42` → controller loads employee #42 → passes `recordId=42` to blade → blade renders `<livewire:qf::data-table-detail :recordId="42" />`

**Company Scoping Analysis:**
- The `$modelClass` is **dynamic** — resolved via `ConfigResolver` from the `$configKey`
- If the model uses `HasCompanyScope`, the global scope filters `findOrFail()` by session company
- No `withoutCompanyScope()` is used

**Existing Safeguards:**
- **CRITICAL DESIGN FLAW:** The code calls `findOrFail()` **TWICE** on the same record:
  1. `loadRecord()` at line 33 (to load the record for display)
  2. `authorizeView()` at line 36 → `resolveRecord()` → another `findOrFail()` (see Item #1)
- The authorization call on line 36 also uses the original `$configKey` (different resolver instance), but both resolve to the same model
- **Order problem:** `loadRecord()` (findOrFail) runs BEFORE authorization check. A user gets a 404 before they even know if they're authorized to view the record.

**Risk Scenario:**
1. The user's session `current_company_id` changes between when the page was loaded and when this component mounts (e.g., browser refresh)
2. `loadRecord()` → `findOrFail()` with CompanyScope → 404 — record not found under current session company
3. The user sees an error page before authorization is even checked

Also, with the **double findOrFail**:
- If the record is deleted between `loadRecord()` and `authorizeView()`, the second `findOrFail()` crashes
- This is extremely unlikely in practice but demonstrates the fragility of the design

**Recommended Fix:**
```php
public function mount(string $configKey, int $recordId, $inline = false, array $returnParams = [])
{
    $this->configKey = $configKey;
    $this->recordId = $recordId;
    $this->returnParams = $returnParams;
    $this->inline = $inline;

    $this->loadConfiguration();
    
    // AUTHORIZE FIRST, then load record
    $modelClass = $this->getConfigResolver()->getModel();
    try {
        app(AuthorizationService::class)->authorizeView(auth()->user(), $recordId, $modelClass);
    } catch (\Exception $e) {
        // Already aborts with 403 in authorizeView
        // Just let it propagate
        throw $e;
    }
    
    $this->loadRecord();
}
```

And in `loadRecord()`:
```php
protected function loadRecord(): void
{
    $modelClass = $this->getConfigResolver()->getModel();
    $relations = array_keys($this->getConfigResolver()->getRelations());
    
    $record = $modelClass::withoutCompanyScope()->with($relations)->find($this->recordId);
    
    if (!$record) {
        throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
            "Record {$this->recordId} not found for model {$modelClass}"
        );
    }
    
    $this->record = $record;
}
```

**Impact of fix:** Since `DataTableDetail` is a **generic, reusable ui-library component**, fixing it here protects **ALL detail pages across ALL modules** that use it.

---

### Item #22: [`DataTableForm.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/DataTables/DataTableForm.php:672)

**Current code:**
```php
670:         DB::transaction(function () {
671:             $record = $this->isEditMode
672:                 ? $this->modelClass::findOrFail($this->recordId)
673:                 : new $this->modelClass();
```

**ID Source Trace:**
- `$this->recordId` is set in [`mount()`](line:80-114) as a Livewire parameter
- For edit mode, the `mount()` also calls `authorizeUpdate()` (line 103), which does another `findOrFail()` via `resolveRecord()` (see Item #1)
- The ID is validated through authorization before reaching this `save()` method

**Company Scoping Analysis:**
- `$this->modelClass` is dynamic — resolved from `ConfigResolver`
- If the model uses `HasCompanyScope`, the global scope applies to `findOrFail()`

**Existing Safeguards:**
- The `save()` method is inside a `DB::transaction()` which would roll back on exception
- The `mount()` method already does authorization which includes a findOrFail check
- **Partial protection:** If the record is deleted between `mount()` and `save()`, the user gets a crash during save

**Risk Scenario:**
1. User opens the edit form for record #42
2. `mount()` calls `authorizeUpdate()` → resolveRecord finds record → authorized
3. Before the user clicks "Save", another admin deletes record #42
4. User clicks "Save" → `save()` is called → `findOrFail(42)` → **ModelNotFoundException**
5. The transaction is rolled back, but the user gets an uncaught 404/error

**Recommended Fix:**
```php
DB::transaction(function () {
    if ($this->isEditMode) {
        $record = $this->modelClass::withoutCompanyScope()->find($this->recordId);
        if (!$record) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
                "Record {$this->recordId} not found — it may have been deleted."
            );
        }
    } else {
        $record = new $this->modelClass();
    }
    // ... rest of save logic
```

**Impact of fix:** `DataTableForm` is a **generic, reusable ui-library component** used by ALL modules for ALL forms. Fixing it here is the highest leverage change possible.

---

### Item #26: [`WizardForm.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/Wizards/WizardForm.php:305)

**Current code:**
```php
303:         DB::transaction(function () {
304:             if ($this->isEditMode) {
305:                 $record = $this->modelClass::findOrFail($this->recordId);
306:             } else {
307:                 $record = new $this->modelClass();
308:             }
```

**ID Source Trace:**
- `$this->recordId` is a Livewire property set during component mount or wizard initialization
- The value is typically passed from a blade template as a Livewire parameter

**Company Scoping Analysis:**
- Dynamic `$this->modelClass` from ConfigResolver
- Same pattern as Items #21 and #22 — if model uses `HasCompanyScope`, the global scope applies

**Existing Safeguards:**
- Inside `DB::transaction()` for rollback safety
- No try-catch around `findOrFail` itself
- No pre-validation of `$this->recordId` existence before the save

**Risk Scenario:**
- Same as Item #22 — record deleted between wizard load and save, or session company changes
- The wizard is multi-step, so the time gap between initial load and final save is even larger than with regular forms

**Recommended Fix:**
```php
DB::transaction(function () {
    if ($this->isEditMode) {
        $record = $this->modelClass::withoutCompanyScope()->find($this->recordId);
        if (!$record) {
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Record not found. It may have been deleted.']);
            return;
        }
    } else {
        $record = new $this->modelClass();
    }
    // ... rest of save logic
```

**Impact of fix:** `WizardForm` is a **generic ui-library wizard component** used by all wizard-based workflows (employee onboarding, payroll run wizard, holiday batch creation, etc.). Fixing it here protects all wizards.

---

### Item #27: [`EmployeeDetail.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/Custom/EmployeeDetail.php:147)

**Current code:**
```php
136:         $this->employee = $modelClass::with([
137:             'employeeProfile',
138:             'employeePosition.jobTitle',
139:             'employeePosition.department',
140:             'employeePosition.manager',
141:             'employeePosition.reportsTo',
142:             'employeePosition.location',
143:             'employeePosition.shift',
144:             'employeePosition.attendancePolicy',
145:             'jobHistory',
146:             'employeeWorkPatterns.workPattern',
147:         ])->findOrFail($this->recordId);
```

**ID Source Trace:**
- `$this->recordId` is set in [`mount()`](line:47-68) as `int $recordId` — a Livewire parameter
- The Employee model is resolved via ConfigResolver (though it could be hardcoded to `Employee::class`)

**Company Scoping Analysis:**
- `Employee` model uses `HasCompanyScope` — `findOrFail()` is scoped by session company
- No `withoutCompanyScope()` — the employee's company must match `session('current_company_id')`

**Existing Safeguards:**
- **None.** No try-catch. No null check. No authorization check.
- Unlike `DataTableDetail.php`, this component doesn't call `AuthorizationService` at all — so there's no fallback authorization

**Risk Scenario:**
1. An HR admin has `current_company_id = 3`
2. They navigate to `/hr/employees/42` — employee #42 belongs to company 3
3. `Employee::findOrFail(42)` succeeds → detail page loads
4. Admin switches company switcher to company 5 (to check something else)
5. Detail page re-renders → `findOrFail(42)` now scoped to company 5 → **404**
6. User sees an error instead of a graceful "not found in current company" message

**Recommended Fix:**
```php
// In mount() or loadData():
$employee = Employee::withoutCompanyScope()
    ->with([... relations ...])
    ->find($this->recordId);

if (!$employee) {
    session()->flash('error', 'Employee not found.');
    $this->redirectRoute('employees.index');
    return;
}

// Check if user has access to this employee's company
$userCompanyId = session('current_company_id');
if ($userCompanyId && $userCompanyId !== 0 && $employee->company_id !== $userCompanyId) {
    session()->flash('error', 'Employee belongs to a different company.');
    $this->redirectRoute('employees.index');
    return;
}

$this->employee = $employee;
```

---

## Summary of Critical Patterns

### Pattern A: `$obj->property ?? 'default'` on potentially null `$obj`
**Affected items:** #10 (PayrollWizardAdjustments:362), #12 (PayrollWizardPreview:408), #19 (PayrollCalculator:754)

```php
// BROKEN — crashes when $obj is null
$name = $obj->name ?? 'Unknown';

// FIXED — PHP 8 null-safe
$name = $obj?->name ?? 'Unknown';

// FIXED — Laravel helper
$name = optional($obj)->name ?? 'Unknown';
```

### Pattern B: Raw route/request parameter → `findOrFail()` with no scoping
**Affected items:** #2 (blade:7), #30 (print controller:33), #31 (import controller:26), #32 (export controller:217)

```php
// BROKEN — no validation, no scoping, no error handling
$record = Model::findOrFail($id);

// FIXED
$record = Model::find($id);
if (!$record) {
    return response()->json(['error' => 'Not found'], 404);
}
```

### Pattern C: Generic ui-library components with dynamic model `findOrFail()`
**Affected items:** #21 (DataTableDetail:68), #22 (DataTableForm:672), #26 (WizardForm:305), #27 (EmployeeDetail:147)

These are the **highest-leverage fixes** — changing these 4 generic components protects ALL modules.

### Pattern D: Double `findOrFail()` (authorization + loadRecord)
**Affected items:** #1 + #21 + #22 (AuthorizationService → DataTableDetail/DataTableForm)

The authorization service calls `findOrFail()` to resolve a record, then the UI component calls `findOrFail()` again to load it for display. Both calls hit the same database record. This is redundant and doubles the risk surface.

### Pattern E: Session-based company scope causing stale-filter 404s
**Affected items:** #3, #4 (wizard and detail components)

When session `current_company_id` changes (via company switcher), components that loaded under one company scope will fail with 404 on re-render because the scope now filters to a different company.

**Recommended approach for all Pattern E items:** Use `withoutCompanyScope()->find()` for record lookup, then manually check company access if needed. This decouples "is the record in the database" from "does the user's current session company match".

---

## Files NOT requiring changes (LOW risk, well-guarded)

These items from the raw findings are already properly guarded and were correctly classified as LOW risk:

| Item | File | Why Safe |
|------|------|----------|
| #5 | EmployeeProfileController.php:14 | Scoped to `Auth::id()` via `where('user_id', Auth::id())` |
| #7 | PayrollRunWizard.php:309 | Guarded by `if ($run)` null check |
| #8 | PayrollRunWizard.php:356 | Guarded by `if (!$run)` early return + session cleanup |
| #9 | PayrollRunWizard.php:438 | Ternary `? find() : null` — only calls find when ID is truthy |
| #11 | PayrollWizardAdjustments.php:419 | Guarded by `$company ?` ternary check |
| #13 | PayrollWizardPreview.php:480 | Guarded by `$company ?` ternary check |
| #14 | ProcessEmployeeBatch.php:104 | Guarded by `if ($run)` inside `failed()` handler |
| #15 | ProcessAttendanceJob.php:27 | Guarded by `if (!$employee)` + logging |
| #16 | LeaveRequestEventListener.php:24 | Guarded by `if ($leaveRequest)` + logging |
| #17 | PayrollCalculator.php:162 | Guarded by `if ($workPattern && ...)` |
| #18 | PayrollCalculator.php:690 | Uses PHP 8 `?->` null-safe operator |
| #20 | EmployeePosition.php:402-411 | Wrapped in `optional()` helper |
| #23 | FilterPanel.php:157 | Scoped to `user_id` via `where('user_id', Auth::id())` |
| #24 | FilterPanel.php:196 | Scoped to `user_id` via `where('user_id', Auth::id())` |
| #25 | FilterPanel.php:572 | Scoped to `user_id` OR `is_global` |
| #28 | ReportViewer.php:34 | Scoped to `user_id` via `where('user_id', Auth::id())` |
| #29 | ReportBuilder.php:38 | Scoped to `user_id` via `where('user_id', Auth::id())` |
| #33 | ExportController.php:449 | Scoped to `user_id` via `where('user_id', auth()->id())` |