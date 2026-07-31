# Phase 1 Infrastructure Relocation Assessment

**Date:** 2026-07-30  
**Status:** READ-ONLY — No files modified  
**Purpose:** Pre-relocation analysis of `ResolvesModels` trait and `RecordNotAccessibleException` before moving them to the ui-library package.

---

## 1. Current Namespaces

| File | Current Namespace | Path |
|------|------------------|------|
| `ResolvesModels` | `App\Traits` | [`app/Traits/ResolvesModels.php`](app/Traits/ResolvesModels.php:3) |
| `RecordNotAccessibleException` | `App\Exceptions` | [`app/Exceptions/RecordNotAccessibleException.php`](app/Exceptions/RecordNotAccessibleException.php:3) |
| `Handler` | `App\Exceptions` | [`app/Exceptions/Handler.php`](app/Exceptions/Handler.php:3) |

### Cross-dependencies between these files

- [`ResolvesModels`](app/Traits/ResolvesModels.php:5) imports `App\Exceptions\RecordNotAccessibleException` — throws it from [`resolveModelOrFail()`](app/Traits/ResolvesModels.php:100)
- [`Handler`](app/Exceptions/Handler.php:32) references `RecordNotAccessibleException` directly (same namespace, no import needed) — catches it in [`register()`](app/Exceptions/Handler.php:32) and [`render()`](app/Exceptions/Handler.php:64)
- `RecordNotAccessibleException` has no dependencies on the other two files

---

## 2. Complete Reference List: `ResolvesModels`

### 2.1 Within quick-hr (`App\` namespace)

| File | Line(s) | Type |
|------|---------|------|
| [`app/Traits/ResolvesModels.php`](app/Traits/ResolvesModels.php:11) | 11, 18, 27, 31, 55, 224 | Self (trait definition + docblock + log messages) |
| [`app/Modules/Hr/Http/Livewire/Payroll/PayrollRunWizard.php`](app/Modules/Hr/Http/Livewire/Payroll/PayrollRunWizard.php:12) | 12 | `use App\Traits\ResolvesModels;` (import) |
| | 18 | `use ResolvesModels;` (trait usage) |
| [`app/Modules/Hr/Http/Livewire/Payroll/PayrollRunDetail.php`](app/Modules/Hr/Http/Livewire/Payroll/PayrollRunDetail.php:8) | 8 | `use App\Traits\ResolvesModels;` (import) |
| | 19 | `use ResolvesModels;` (trait usage) |

**Total quick-hr consumers: 2 files** (both in `app/Modules/Hr/Http/Livewire/Payroll/`)

### 2.2 Within ui-library (`QuickerFaster\UILibrary\` namespace)

| File | Line(s) | Type |
|------|---------|------|
| [`src/Http/Livewire/DataTables/DataTableDetail.php`](src/Http/Livewire/DataTables/DataTableDetail.php:9) | 9 | `use App\Traits\ResolvesModels;` (import) |
| | 13 | `use ResolvesModels;` (trait usage) |
| [`src/Http/Livewire/DataTables/DataTableForm.php`](src/Http/Livewire/DataTables/DataTableForm.php:22) | 22 | `use App\Traits\ResolvesModels;` (import) |
| | 29 | `use ResolvesModels;` (trait usage) |
| [`src/Http/Livewire/Wizards/WizardForm.php`](src/Http/Livewire/Wizards/WizardForm.php:14) | 14 | `use App\Traits\ResolvesModels;` (import) |
| | 20 | `use ResolvesModels;` (trait usage) |
| [`src/Http/Livewire/Custom/EmployeeDetail.php`](src/Http/Livewire/Custom/EmployeeDetail.php:10) | 10 | `use App\Traits\ResolvesModels;` (import) |
| | 14 | `use ResolvesModels;` (trait usage) |
| [`src/Http/Controllers/Prints/GenericDetailPagePrintController.php`](src/Http/Controllers/Prints/GenericDetailPagePrintController.php:5) | 5 | `use App\Traits\ResolvesModels;` (import) |
| | 13 | `use ResolvesModels;` (trait usage) |

**Total ui-library consumers: 5 files**

---

## 3. Complete Reference List: `RecordNotAccessibleException`

### 3.1 Within quick-hr (`App\` namespace)

| File | Line(s) | Type |
|------|---------|------|
| [`app/Exceptions/RecordNotAccessibleException.php`](app/Exceptions/RecordNotAccessibleException.php:9) | 9, 19, 50 | Self (class definition + docblock) |
| [`app/Traits/ResolvesModels.php`](app/Traits/ResolvesModels.php:5) | 5 | `use App\Exceptions\RecordNotAccessibleException;` (import) |
| | 76, 86 | Docblock references |
| | 100 | `throw new RecordNotAccessibleException(...)` |
| [`app/Exceptions/Handler.php`](app/Exceptions/Handler.php:32) | 32, 33, 54, 63, 64, 78, 80 | Type hints + `instanceof` checks (same namespace, no import) |
| [`app/Modules/Admin/Services/AuthorizationService.php`](app/Modules/Admin/Services/AuthorizationService.php:5) | 5 | `use App\Exceptions\RecordNotAccessibleException;` (import) |
| | 277 | `catch (RecordNotAccessibleException $e)` |
| | 412 | `throw RecordNotAccessibleException::notFound(...)` |

**Total quick-hr consumers: 3 files** (ResolvesModels, Handler, AuthorizationService)

### 3.2 Within ui-library

**No references found.** The ui-library does not directly use `RecordNotAccessibleException`. However, it transitively depends on it because `ResolvesModels::resolveModelOrFail()` throws it.

---

## 4. ui-library `composer.json` Autoload Configuration

**File:** `/Users/mac/Projects/Libraries/ui-library/composer.json`

```json
{
    "name": "quicker-faster/ui-library",
    "description": "A Laravel livewier ui components",
    "type": "library",
    "license": "MIT",
    "autoload": {
        "psr-4": {
            "QuickerFaster\\UILibrary\\": "src/"
        }
    },
    "require": {
        "livewire/livewire": "^3",
        "barryvdh/laravel-dompdf": "^3.0",
        "maatwebsite/excel": "^3.1",
        "laravel/fortify": "^1.0",
        "laravel/socialite": "^5.0",
        "spatie/laravel-permission": "^6.21",
        "spatie/laravel-onboard": "^2.6"
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
```

**Key observations:**
- The ui-library has its own PSR-4 root: `QuickerFaster\UILibrary\` → `src/`
- There is **no** autoload mapping for `App\` — yet 5 files import `App\Traits\ResolvesModels`
- This means the ui-library **depends on quick-hr's autoloader** to resolve `App\Traits\ResolvesModels`
- The ui-library has **no** explicit `require` or `repositories` entry pointing to quick-hr

---

## 5. quick-hr `composer.json` Autoload Configuration

**File:** `/Users/mac/Projects/LaravelProjects/quick-hr/composer.json`

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Database\\Factories\\": "database/factories/",
        "Database\\Seeders\\": "database/seeders/"
    }
}
```

**Key observations:**
- `App\` maps to `app/` — this covers `App\Traits\ResolvesModels` and `App\Exceptions\RecordNotAccessibleException`
- There is **no** separate PSR-4 entry for `App\Modules\` — modules are covered by the blanket `App\` → `app/` mapping
- The `app/Modules/` directory structure follows `App\Modules\` namespace convention implicitly

---

## 6. Recommended Target Locations and Namespace Changes

### 6.1 Recommendation: Move both files to ui-library

Both `ResolvesModels` and `RecordNotAccessibleException` should be relocated to the ui-library package under the `QuickerFaster\UILibrary\` namespace. Rationale:

1. **ui-library is the heavier consumer** — 5 files vs. 2 in quick-hr for `ResolvesModels`
2. **Both are generic infrastructure** — neither contains HR-specific business logic; they are plumbing for safe model resolution
3. **Eliminates the broken dependency** — ui-library currently depends on `App\` namespace without declaring it, which is fragile
4. **Co-location of coupled code** — `ResolvesModels` throws `RecordNotAccessibleException`, so they must move together

### 6.2 Proposed New Locations

| Current Path | Current Namespace | Proposed Path | Proposed Namespace |
|---|---|---|---|
| `app/Traits/ResolvesModels.php` | `App\Traits` | `src/Concerns/ResolvesModels.php` | `QuickerFaster\UILibrary\Concerns` |
| `app/Exceptions/RecordNotAccessibleException.php` | `App\Exceptions` | `src/Exceptions/RecordNotAccessibleException.php` | `QuickerFaster\UILibrary\Exceptions` |

> **Note on directory name:** `Concerns/` is preferred over `Traits/` in the ui-library because Laravel ecosystem conventions (e.g., Laravel Nova, Livewire itself) use `Concerns/` for trait directories in packages.

### 6.3 Files Requiring Import Updates

#### In ui-library (5 files — simple namespace change):

| File | Current Import | New Import |
|------|---------------|------------|
| `src/Http/Livewire/DataTables/DataTableDetail.php:9` | `use App\Traits\ResolvesModels;` | `use QuickerFaster\UILibrary\Concerns\ResolvesModels;` |
| `src/Http/Livewire/DataTables/DataTableForm.php:22` | `use App\Traits\ResolvesModels;` | `use QuickerFaster\UILibrary\Concerns\ResolvesModels;` |
| `src/Http/Livewire/Wizards/WizardForm.php:14` | `use App\Traits\ResolvesModels;` | `use QuickerFaster\UILibrary\Concerns\ResolvesModels;` |
| `src/Http/Livewire/Custom/EmployeeDetail.php:10` | `use App\Traits\ResolvesModels;` | `use QuickerFaster\UILibrary\Concerns\ResolvesModels;` |
| `src/Http/Controllers/Prints/GenericDetailPagePrintController.php:5` | `use App\Traits\ResolvesModels;` | `use QuickerFaster\UILibrary\Concerns\ResolvesModels;` |

#### In quick-hr (4 files):

| File | Current Import | New Import |
|------|---------------|------------|
| `app/Modules/Hr/Http/Livewire/Payroll/PayrollRunWizard.php:12` | `use App\Traits\ResolvesModels;` | `use QuickerFaster\UILibrary\Concerns\ResolvesModels;` |
| `app/Modules/Hr/Http/Livewire/Payroll/PayrollRunDetail.php:8` | `use App\Traits\ResolvesModels;` | `use QuickerFaster\UILibrary\Concerns\ResolvesModels;` |
| `app/Exceptions/Handler.php` | (same namespace, no import) | Add: `use QuickerFaster\UILibrary\Exceptions\RecordNotAccessibleException;` |
| `app/Modules/Admin/Services/AuthorizationService.php:5` | `use App\Exceptions\RecordNotAccessibleException;` | `use QuickerFaster\UILibrary\Exceptions\RecordNotAccessibleException;` |

### 6.4 Internal Import Update (within moved files)

| File | Current Internal Import | New Internal Import |
|------|------------------------|---------------------|
| `ResolvesModels.php:5` | `use App\Exceptions\RecordNotAccessibleException;` | `use QuickerFaster\UILibrary\Exceptions\RecordNotAccessibleException;` |

### 6.5 Files to Delete After Relocation

| Path | Reason |
|------|--------|
| `app/Traits/ResolvesModels.php` | Moved to `src/Concerns/ResolvesModels.php` |
| `app/Exceptions/RecordNotAccessibleException.php` | Moved to `src/Exceptions/RecordNotAccessibleException.php` |

> **Note:** `app/Exceptions/Handler.php` stays in quick-hr — it is Laravel's global exception handler and is application-specific.

### 6.6 Migration Order (Recommended Sequence)

1. **Create** `src/Concerns/ResolvesModels.php` in ui-library with updated namespace and internal import
2. **Create** `src/Exceptions/RecordNotAccessibleException.php` in ui-library with updated namespace
3. **Update** all 5 ui-library consumers to use new namespace
4. **Update** all 4 quick-hr consumers to use new namespace
5. **Delete** `app/Traits/ResolvesModels.php` and `app/Exceptions/RecordNotAccessibleException.php` from quick-hr
6. **Run** `composer dump-autoload` in both projects
7. **Verify** no remaining references to `App\Traits\ResolvesModels` or `App\Exceptions\RecordNotAccessibleException` via grep

### 6.7 Risk Assessment

| Risk | Severity | Mitigation |
|------|----------|------------|
| ui-library not requiring quick-hr as a dependency — after move, ui-library is self-contained | **None** (this is the goal) | N/A |
| `Handler.php` type-hints `RecordNotAccessibleException` without import (same namespace) | **Medium** | Must add explicit `use` import after move |
| `AuthorizationService.php` catches `RecordNotAccessibleException` | **Low** | Simple import change |
| Stale `App\Traits\ResolvesModels` references in docblocks/comments | **Low** | Grep after migration to catch any missed references |

---

## Summary

| Metric | Value |
|--------|-------|
| Files to move | 2 |
| ui-library files to update | 5 |
| quick-hr files to update | 4 |
| Total import changes | 11 lines |
| Files to delete from quick-hr | 2 |
| Cross-package dependency eliminated | `App\Traits\ResolvesModels` from ui-library |
