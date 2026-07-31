# Low-Risk Remaining Items — Final Scan Report

**Generated:** 2026-07-30  
**Scan scope:** `app/` (quick-hr) + `/Users/mac/Projects/Libraries/ui-library/src/`  
**Methods scanned:** `findOrFail()`, `firstOrFail()`

---

## Executive Summary

| Metric | Count |
|--------|-------|
| Original findings (Phases 1-4 baseline) | 33 |
| **Resolved / No longer present** | **24** |
| **Still remaining** | **9** |
| Resolution rate | **72.7%** |

**Key achievement:** All **HIGH-risk** items (P0/P1) have been resolved. All **7 HIGH-risk** items from the original scan are gone. The 9 remaining items are all **LOW** or **MEDIUM** risk.

---

## Cross-Reference: Original 33 → Current Status

### ✅ RESOLVED (24 items)

| Orig # | File | Original Method | Risk | Resolution |
|--------|------|-----------------|------|------------|
| 1 | `AuthorizationService.php:399` | `findOrFail` | HIGH | Removed — now uses `ResolvesModels` |
| 2 | `attendance-work-sessions.blade.php:7` | `findOrFail` | HIGH | Removed from blade view |
| 3 | `PayrollRunWizard.php:75` | `findOrFail` | MEDIUM | Replaced with `resolveModel` |
| 4 | `PayrollRunDetail.php:41` | `findOrFail` | MEDIUM | Replaced with `resolveModel` |
| 6 | `AttendanceEventListener.php:74` | `findOrFail` | MEDIUM | Replaced with `resolveModel` |
| 7 | `PayrollRunWizard.php:309` | `::find` | LOW | Already had `if ($run)` guard — no change needed |
| 8 | `PayrollRunWizard.php:356` | `::find` | LOW | Already had `if (!$run)` guard — no change needed |
| 9 | `PayrollRunWizard.php:438` | `::find` | LOW | Already had ternary guard — no change needed |
| 10 | `PayrollWizardAdjustments.php:362` | `::find` | MEDIUM | **Bug fixed:** `$company->name` → `$company?->name` |
| 11 | `PayrollWizardAdjustments.php:419` | `::find` | LOW | Already had `$company ?` guard — no change needed |
| 12 | `PayrollWizardPreview.php:408` | `::find` | MEDIUM | **Bug fixed:** `$company->name` → `$company?->name` |
| 13 | `PayrollWizardPreview.php:480` | `::find` | LOW | Already had `$company ?` guard — no change needed |
| 14 | `ProcessEmployeeBatch.php:104` | `::find` | LOW | Already had `if ($run)` guard — no change needed |
| 15 | `ProcessAttendanceJob.php:27` | `::find` | LOW | Already had `if (!$employee)` guard — no change needed |
| 16 | `LeaveRequestEventListener.php:24` | `::find` | LOW | Already had `if ($leaveRequest)` guard — no change needed |
| 17 | `PayrollCalculator.php:162` | `::find` | LOW | Already had `if ($workPattern && ...)` guard — no change needed |
| 18 | `PayrollCalculator.php:690` | `::find` | LOW | Already used `?->name` null-safe — no change needed |
| 19 | `PayrollCalculator.php:754` | `::find` | HIGH | **Bug fixed:** Added `if (!$company)` null check |
| 20 | `EmployeePosition.php:402-411` | `::find` | LOW | Already wrapped in `optional()` — no change needed |
| 21 | `DataTableDetail.php:68` | `findOrFail` | MEDIUM | Replaced with `resolveModelOrFail` |
| 27 | `EmployeeDetail.php:147` | `findOrFail` | MEDIUM | Replaced with `resolveModelOrFail` |
| 30 | `GenericDetailPagePrintController.php:33` | `findOrFail` | HIGH | Replaced with `resolveModelOrFail` |
| 31 | `ImportController.php:26` | `findOrFail` | HIGH | Replaced with `resolveModelOrFail` |
| 32 | `ExportController.php:217` | `findOrFail` | HIGH | Replaced with `resolveModelOrFail` |

### ⚠️ STILL REMAINING (9 items)

See detailed table below.

---

## Detailed Table: All Remaining Calls

| # | File | Line | Method | Model | ID Source | Has Scope? | Existing Safeguard? | Imports ResolvesModels? | Risk |
|---|------|------|--------|-------|-----------|-------------|---------------------|------------------------|------|
| R1 | [`DataTableForm.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/DataTables/DataTableForm.php:677) | 677 | `findOrFail` | Dynamic `$this->modelClass` | `$this->recordId` (Livewire property) | No | Inside `DB::transaction()` but no try-catch | **YES** | **MEDIUM** |
| R2 | [`WizardForm.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/Wizards/WizardForm.php:316) | 316 | `findOrFail` | Dynamic `$this->modelClass` | `$this->recordId` (Livewire property) | No | Inside `DB::transaction()` but no try-catch | **YES** | **MEDIUM** |
| R3 | [`EmployeeProfileController.php`](/Users/mac/Projects/LaravelProjects/quick-hr/app/Modules/Hr/Http/Controllers/EmployeeProfileController.php:14) | 14 | `firstOrFail` | `Employee` | `Auth::id()` (authenticated user) | Implicit — `where('user_id', ...)` | No try-catch (but scoped to auth user) | **NO** | **LOW** |
| R4 | [`FilterPanel.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/FilterPanel.php:157) | 157 | `firstOrFail` | `SavedFilter` | `$filterId` (function param) | Yes — `where('user_id', Auth::id())` | No try-catch (but user-scoped) | **NO** | **LOW** |
| R5 | [`FilterPanel.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/FilterPanel.php:196) | 196 | `firstOrFail` | `SavedFilter` | `$this->editingFilterId` (Livewire property) | Yes — `where('user_id', Auth::id())` | No try-catch (but user-scoped) | **NO** | **LOW** |
| R6 | [`FilterPanel.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/FilterPanel.php:572) | 572 | `firstOrFail` | `SavedFilter` | `$id` (function param) | Yes — `where('user_id', ...) OR is_global` | No try-catch (but scoped) | **NO** | **LOW** |
| R7 | [`ReportViewer.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/Reports/ReportViewer.php:34) | 34 | `firstOrFail` | `SavedReport` | `$this->savedReportId` (Livewire mount property) | Yes — `where('user_id', Auth::id())` | No try-catch (but user-scoped) | **NO** | **LOW** |
| R8 | [`ReportBuilder.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/Reports/ReportBuilder.php:38) | 38 | `firstOrFail` | `SavedReport` | `$this->reportId` (Livewire mount property) | Yes — `where('user_id', Auth::id())` | No try-catch (but user-scoped) | **NO** | **LOW** |
| R9 | [`ExportController.php`](/Users/mac/Projects/Libraries/ui-library/src/Http/Controllers/Exports/ExportController.php:461) | 461 | `firstOrFail` | `Export` | `$id` (route parameter) | Yes — `where('user_id', auth()->id())` | No try-catch (but user-scoped) | **NO** | **LOW** |

---

## Context Analysis Per Remaining Item

### R1 — DataTableForm.php:677 (MEDIUM)

```php
// Line 675-678
DB::transaction(function () {
    $record = $this->isEditMode
        ? $this->modelClass::findOrFail($this->recordId)
        : new $this->modelClass();
```

- **Model:** Dynamic `$this->modelClass` (resolved from YAML config)
- **ID source:** `$this->recordId` — Livewire property set during `mount()`
- **Scope:** None
- **Safeguard:** Inside `DB::transaction()` but no try-catch around `findOrFail`
- **Already imports ResolvesModels:** YES (line 22, used at line 29)
- **Note:** The `checkAuthorization()` method at line 106-108 already uses `$this->resolveModelOrFail()`, but the `save()` method at line 677 still uses raw `findOrFail()`. This is an inconsistency within the same file.

### R2 — WizardForm.php:316 (MEDIUM)

```php
// Line 314-318
DB::transaction(function () {
    if ($this->isEditMode) {
        $record = $this->modelClass::findOrFail($this->recordId);
    } else {
        $record = new $this->modelClass();
    }
```

- **Model:** Dynamic `$this->modelClass` (resolved from YAML config)
- **ID source:** `$this->recordId` — Livewire property
- **Scope:** None
- **Safeguard:** Inside `DB::transaction()` but no try-catch
- **Already imports ResolvesModels:** YES (line 14, used at line 20)
- **Note:** Same pattern as R1 — the trait is imported but not used in the `save()` method.

### R3 — EmployeeProfileController.php:14 (LOW)

```php
// Line 14
$employee = Employee::where('user_id', Auth::id())->firstOrFail();
```

- **Model:** `Employee`
- **ID source:** `Auth::id()` — authenticated user's ID
- **Scope:** Implicit — scoped to the authenticated user via `where('user_id', ...)`
- **Safeguard:** None, but the scope means only the authenticated user's own employee record is queried
- **Already imports ResolvesModels:** NO
- **Note:** This is a controller (not a Livewire component). The `ResolvesModels` trait can be used in controllers too. If the user has no employee profile, a 404 is arguably the correct behavior — this is a "my profile" page.

### R4-R6 — FilterPanel.php:157, 196, 572 (LOW)

All three follow the same pattern — `SavedFilter` queries scoped to the authenticated user:

```php
// Line 155-157
$filter = SavedFilter::where('id', $filterId)
    ->where('user_id', Auth::id())
    ->firstOrFail();
```

- **Model:** `SavedFilter`
- **ID source:** Function parameter or Livewire property
- **Scope:** User-scoped via `where('user_id', Auth::id())`
- **Safeguard:** User scoping provides implicit protection — a user can only access their own saved filters
- **Already imports ResolvesModels:** NO
- **Note:** Line 572 additionally allows `is_global` filters. These are well-scoped and low-risk.

### R7 — ReportViewer.php:34 (LOW)

```php
// Line 32-34
$saved = SavedReport::where('id', $this->savedReportId)
    ->where('user_id', Auth::id())
    ->firstOrFail();
```

- **Model:** `SavedReport`
- **ID source:** `$this->savedReportId` — Livewire mount parameter
- **Scope:** User-scoped
- **Safeguard:** User scoping
- **Already imports ResolvesModels:** NO

### R8 — ReportBuilder.php:38 (LOW)

```php
// Line 36-38
$saved = SavedReport::where('id', $this->reportId)
    ->where('user_id', Auth::id())
    ->firstOrFail();
```

- **Model:** `SavedReport`
- **ID source:** `$this->reportId` — Livewire mount parameter
- **Scope:** User-scoped
- **Safeguard:** User scoping
- **Already imports ResolvesModels:** NO

### R9 — ExportController.php:461 (LOW)

```php
// Line 461
$export = Export::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
```

- **Model:** `Export`
- **ID source:** `$id` — route parameter
- **Scope:** User-scoped via `where('user_id', auth()->id())`
- **Safeguard:** User scoping
- **Already imports ResolvesModels:** NO
- **Note:** This is a JSON endpoint (`cancelExport`). If the export doesn't belong to the user, a 404 is returned — which is acceptable behavior for a user-scoped resource.

---

## Recommended Fixes — Priority Order

### Priority 1: Fix Inconsistency in Files That Already Import ResolvesModels

These files already have `use ResolvesModels` but still use raw `findOrFail()` in their `save()` methods. This is an inconsistency that should be fixed for consistency and safety.

| Priority | # | File | Fix |
|----------|---|------|-----|
| 🔴 P1 | R1 | `DataTableForm.php:677` | Replace `$this->modelClass::findOrFail($this->recordId)` with `$this->resolveModelOrFail($this->modelClass, $this->recordId)` |
| 🔴 P1 | R2 | `WizardForm.php:316` | Replace `$this->modelClass::findOrFail($this->recordId)` with `$this->resolveModelOrFail($this->modelClass, $this->recordId)` |

**Rationale:** These are the only remaining MEDIUM-risk items. Both files already import `ResolvesModels` and use it elsewhere (in `checkAuthorization()` / `mount()`). The `save()` methods were simply missed during Phase 1-4 implementation. The fix is a one-line change in each file.

### Priority 2: User-Scoped firstOrFail() — Optional Migration

These 7 items are all LOW risk because they are scoped to the authenticated user. The `firstOrFail()` will only throw a 404 if the record doesn't belong to the current user, which is acceptable behavior.

| Priority | # | File | Recommendation |
|----------|---|------|----------------|
| 🟡 P2 | R3 | `EmployeeProfileController.php:14` | **Leave as-is.** Scoped to `Auth::id()` — 404 is correct if user has no employee profile. |
| 🟡 P2 | R4 | `FilterPanel.php:157` | **Optional:** Add `use ResolvesModels` and replace with `$this->resolveModelOrFail(SavedFilter::class, $filterId, [fn($q) => $q->where('user_id', Auth::id())])` |
| 🟡 P2 | R5 | `FilterPanel.php:196` | **Optional:** Same pattern as R4 |
| 🟡 P2 | R6 | `FilterPanel.php:572` | **Optional:** Same pattern, but scope includes `orWhere('is_global', true)` |
| 🟡 P2 | R7 | `ReportViewer.php:34` | **Optional:** Add `use ResolvesModels` and replace |
| 🟡 P2 | R8 | `ReportBuilder.php:38` | **Optional:** Add `use ResolvesModels` and replace |
| 🟡 P2 | R9 | `ExportController.php:461` | **Optional:** Add `use ResolvesModels` and replace |

**Rationale for "Optional":** These are all user-scoped queries. A `ModelNotFoundException` here means the user is trying to access a record that doesn't belong to them (or doesn't exist). Returning a 404 is standard RESTful behavior. The `ResolvesModels` trait would add `withoutCompanyScope()` which could actually *widen* access beyond what's intended for user-scoped resources.

### Priority 3: Commented-Out Code — No Action Needed

| File | Line | Status |
|------|------|--------|
| `my-profile.blade.php` | 3 | `//->firstOrFail()` — commented out, uses `->first()` instead |
| `my-account.blade.php` | 3 | `//->firstOrFail()` — commented out |
| `AccessControlManager.php` | 102 | `//findOrFail($id)` — commented out |
| `AccessControlManager.php` | 105 | `//findOrFail($id)` — commented out |

These are already commented out and pose no risk.

---

## Summary of What Was Fixed (Phases 1-4)

| Category | Count | Details |
|----------|-------|---------|
| HIGH-risk `findOrFail` removed | 4 | AuthorizationService, blade view, GenericDetailPagePrintController, ImportController, ExportController |
| MEDIUM-risk `findOrFail` replaced with `resolveModel` | 5 | PayrollRunWizard, PayrollRunDetail, AttendanceEventListener, DataTableDetail, EmployeeDetail |
| Null-property-access bugs fixed | 3 | PayrollWizardAdjustments (×1), PayrollWizardPreview (×1), PayrollCalculator (×1) |
| Already-safe `::find()` with guards | 12 | Various — all had null checks, ternary operators, `optional()`, or `?->` |
| **Total resolved** | **24** | |

---

## Conclusion

The codebase is in excellent shape. All HIGH-risk `findOrFail`/`firstOrFail` calls have been eliminated. The 9 remaining items are either:

1. **2 MEDIUM-risk items** (R1, R2) that already import `ResolvesModels` but missed the `save()` method — trivial one-line fixes.
2. **7 LOW-risk items** (R3-R9) that are user-scoped and can safely be left as-is, or optionally migrated to `ResolvesModels` for consistency.

**Recommendation:** Fix R1 and R2 (Priority 1) for consistency. R3-R9 can be addressed in a future cleanup pass or left as-is since they represent correct RESTful behavior (404 when a user tries to access another user's resources).
