# `findOrFail()` Resolution — Implementation Checklist

**Source:** [`plans/findOrFail-resolution-plan.md`](plans/findOrFail-resolution-plan.md)  
**Date:** 2026-07-30  
**Total files:** 2 new, 17 modified  
**Scope:** 33 occurrences across `app/` and ui-library

---

## Phase 0: Pre-Implementation Validation

- [ ] Run full test suite baseline: `php artisan test`
- [ ] Verify `composer.json` PHP requirement is `>=8.0` (null-safe `?->` requires PHP 8+)
- [ ] Create backup branch: `git checkout -b fix/findorfail-backup`

---

## Phase 1: Infrastructure (Zero User Impact)

### 1.1 — CREATE NEW: `app/Exceptions/RecordNotAccessibleException.php`

**Action:** Create new file  
**Purpose:** Domain-specific exception replacing raw `ModelNotFoundException` with rich context (HTTP status, user message, redirect route, debug context).

```php
<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

/**
 * RecordNotAccessibleException
 *
 * Thrown when a model record cannot be accessed by the current user.
 * Replaces raw ModelNotFoundException with rich context that the global
 * exception handler can use to render appropriate responses.
 *
 * Distinguishes between:
 *  - 404: Record genuinely does not exist in the database
 *  - 403: Record exists but the current user/company does not have access
 */
class RecordNotAccessibleException extends RuntimeException
{
    /**
     * HTTP status code (404 or 403).
     *
     * @var int
     */
    protected int $httpStatusCode;

    /**
     * User-friendly message safe to display in the UI.
     *
     * @var string
     */
    protected string $userMessage;

    /**
     * Suggested redirect route name.
     *
     * @var string|null
     */
    protected ?string $redirectRoute;

    /**
     * Contextual data for logging/debugging.
     *
     * @var array<string, mixed>
     */
    protected array $context;

    /**
     * Create a new RecordNotAccessibleException.
     *
     * @param  string      $userMessage   Human-readable message for the UI
     * @param  int         $httpStatusCode HTTP status: 404 (not found) or 403 (forbidden)
     * @param  array       $context        Debug/log context: model class, ID, user, etc.
     * @param  string|null $redirectRoute  Suggested redirect route name
     * @param  Throwable   $previous       Previous exception for chaining
     */
    public function __construct(
        string $userMessage = 'The requested record was not found.',
        int $httpStatusCode = 404,
        array $context = [],
        ?string $redirectRoute = null,
        ?Throwable $previous = null
    ) {
        parent::__construct($userMessage, $httpStatusCode, $previous);

        $this->userMessage   = $userMessage;
        $this->httpStatusCode = $httpStatusCode;
        $this->context       = $context;
        $this->redirectRoute = $redirectRoute;
    }

    /**
     * Get the HTTP status code.
     */
    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    /**
     * Get the user-friendly message.
     */
    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    /**
     * Get the suggested redirect route, if any.
     */
    public function getRedirectRoute(): ?string
    {
        return $this->redirectRoute;
    }

    /**
     * Get the debug/log context.
     *
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return array_merge($this->context, [
            'exception_class' => static::class,
            'http_status'     => $this->httpStatusCode,
            'timestamp'       => now()->toIso8601String(),
        ]);
    }

    /**
     * Create an exception for a record that belongs to a different company.
     *
     * @param  string $modelClass  Model class name
     * @param  mixed  $id          Record ID
     * @param  int    $recordCompanyId  The company the record belongs to
     * @param  int    $userCompanyId    The user's current session company
     * @return static
     */
    public static function differentCompany(
        string $modelClass,
        $id,
        int $recordCompanyId,
        int $userCompanyId
    ): self {
        $modelName = class_basename($modelClass);

        return new self(
            "This {$modelName} belongs to a different company and cannot be accessed from your current company view. Please switch to the appropriate company.",
            403,
            [
                'model_class'      => $modelClass,
                'record_id'        => $id,
                'record_company_id' => $recordCompanyId,
                'user_company_id'  => $userCompanyId,
            ],
            null // No suggested redirect — user should switch companies
        );
    }

    /**
     * Create an exception for a record that truly does not exist.
     *
     * @param  string      $modelClass
     * @param  mixed       $id
     * @param  string|null $redirectRoute
     * @return static
     */
    public static function notFound(string $modelClass, $id, ?string $redirectRoute = null): self
    {
        $modelName = class_basename($modelClass);

        return new self(
            "The requested {$modelName} could not be found. It may have been deleted or the link is invalid.",
            404,
            [
                'model_class' => $modelClass,
                'record_id'   => $id,
            ],
            $redirectRoute
        );
    }
}
```

---

### 1.2 — CREATE NEW: `app/Traits/ResolvesModels.php`

**Action:** Create new file  
**Purpose:** Reusable trait for Livewire components and controllers that standardizes model resolution with proper scoping, error handling, and user feedback.

```php
<?php

namespace App\Traits;

use App\Exceptions\RecordNotAccessibleException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

/**
 * Trait ResolvesModels
 *
 * Provides safe, scope-aware model resolution methods for Livewire components
 * and controllers. Designed to replace all findOrFail() / firstOrFail() calls
 * across the application.
 *
 * Usage in a Livewire component:
 *   use ResolvesModels;
 *
 *   $record = $this->resolveModel(PayrollRun::class, $this->payrollRunId);
 *   if (!$record) {
 *       $this->flashAndRedirect('error', 'Payroll run not found.', 'payroll-runs.index');
 *       return;
 *   }
 *
 * Usage in a controller:
 *   use ResolvesModels;
 *
 *   $record = $this->resolveModelOrFail(Employee::class, $id);
 */
trait ResolvesModels
{
    /**
     * Safely resolve a model by ID with optional scoping.
     *
     * Uses withoutCompanyScope() by default to ensure the record is found
     * regardless of the user's current session company. Company access
     * should be checked separately as an authorization concern.
     *
     * @param  string      $modelClass  Fully-qualified model class name
     * @param  int|string  $id          Primary key value
     * @param  array       $scopes      Optional additional query scopes.
     *                                  Each element is a closure: fn($query) => $query->where(...)
     * @return Model|null               The model instance or null if not found
     */
    public function resolveModel(string $modelClass, $id, array $scopes = []): ?Model
    {
        // Validate ID is non-empty and positive (for integer IDs)
        if (empty($id) || (is_int($id) && $id <= 0)) {
            return null;
        }

        // Validate the class exists
        if (!class_exists($modelClass)) {
            Log::warning('ResolvesModels: Attempted to resolve non-existent model class', [
                'model_class' => $modelClass,
                'id'          => $id,
            ]);
            return null;
        }

        // Build the query
        $query = $modelClass::withoutCompanyScope();

        // Apply additional scopes
        foreach ($scopes as $scope) {
            if (is_callable($scope)) {
                $query = $scope($query);
            }
        }

        return $query->find($id);
    }

    /**
     * Resolve a model or throw a RecordNotAccessibleException.
     *
     * @param  string      $modelClass    Fully-qualified model class name
     * @param  int|string  $id            Primary key value
     * @param  array       $scopes        Optional additional query scopes
     * @param  int         $httpStatus    HTTP status code (404 or 403)
     * @param  string|null $message       Custom user-facing message
     * @param  string|null $redirectRoute Route name to suggest for redirection
     * @return Model
     *
     * @throws RecordNotAccessibleException
     */
    public function resolveModelOrFail(
        string $modelClass,
        $id,
        array $scopes = [],
        int $httpStatus = 404,
        ?string $message = null,
        ?string $redirectRoute = null
    ): Model {
        $record = $this->resolveModel($modelClass, $id, $scopes);

        if (!$record) {
            $modelName = class_basename($modelClass);
            throw new RecordNotAccessibleException(
                $message ?? "{$modelName} not found.",
                $httpStatus,
                [
                    'model_class' => $modelClass,
                    'id'          => $id,
                    'scopes'      => $scopes,
                ],
                $redirectRoute
            );
        }

        return $record;
    }

    /**
     * Resolve a model scoped to a specific company.
     *
     * Designed for multi-tenant scenarios where the user should only access
     * records belonging to a given company. Uses withoutCompanyScope() to
     * bypass the session-based global scope, then applies an explicit
     * company_id WHERE clause.
     *
     * @param  string      $modelClass  Fully-qualified model class name
     * @param  int|string  $id          Primary key value
     * @param  int         $companyId   Company ID to scope to
     * @return Model|null
     */
    public function resolveModelForCompany(string $modelClass, $id, int $companyId): ?Model
    {
        return $this->resolveModel($modelClass, $id, [
            function ($query) use ($companyId) {
                return $query->where('company_id', $companyId);
            },
        ]);
    }

    /**
     * Validate that the resolved record belongs to the user's current session company.
     *
     * Call this AFTER resolving a model with resolveModel() to check company access.
     * If the session company is 0 (All Companies mode), access is always granted.
     *
     * @param  Model $record  The resolved model instance
     * @return bool           True if access is allowed, false otherwise
     */
    public function checkCompanyAccess(Model $record): bool
    {
        $sessionCompanyId = Session::get('current_company_id');

        // All Companies mode — super admin access
        if (empty($sessionCompanyId) || $sessionCompanyId === 0) {
            return true;
        }

        // Check if the model has a company_id attribute
        if (!array_key_exists('company_id', $record->getAttributes())) {
            return true; // Model doesn't support company scoping
        }

        return (int) $record->company_id === (int) $sessionCompanyId;
    }

    /**
     * Flash a message to the session and redirect.
     *
     * Works from both Livewire components and controllers. In Livewire,
     * dispatches a showAlert browser event so the UI can display a toast.
     *
     * @param  string $type     Alert type: 'success', 'error', 'warning', 'info'
     * @param  string $message  User-facing message
     * @param  string $route    Route name to redirect to
     * @param  array  $params   Optional route parameters
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function flashAndRedirect(
        string $type,
        string $message,
        string $route,
        array $params = []
    ) {
        session()->flash($type, $message);

        // If this is a Livewire component, also dispatch a browser event
        if (method_exists($this, 'dispatch')) {
            $this->dispatch('showAlert', [
                'type'    => $type,
                'message' => $message,
            ]);
        }

        if (method_exists($this, 'redirectRoute')) {
            // Livewire redirect
            return $this->redirectRoute($route, $params);
        }

        // Controller redirect
        return redirect()->route($route, $params)->with($type, $message);
    }

    /**
     * Clean up wizard session data and redirect with an error message.
     *
     * Used when a wizard component discovers that its backing record has
     * been deleted or is no longer accessible. Prevents the user from
     * being stuck with stale wizard state.
     *
     * @param  string $wizardId      Session key for the wizard (e.g., 'payroll-wizard-' . auth()->id())
     * @param  string $errorMessage  User-facing error message
     * @param  string $fallbackRoute Route to redirect to
     * @param  array  $params        Optional route parameters
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function cleanupWizardSession(
        string $wizardId,
        string $errorMessage,
        string $fallbackRoute,
        array $params = []
    ) {
        // Clear all wizard-related session data
        if (session()->has($wizardId)) {
            session()->forget($wizardId);
        }

        Log::info('ResolvesModels: Cleaned up stale wizard session', [
            'wizard_id' => $wizardId,
            'user_id'   => auth()->id() ?? 'guest',
            'reason'    => $errorMessage,
        ]);

        return $this->flashAndRedirect('error', $errorMessage, $fallbackRoute, $params);
    }
}
```

---

### 1.3 — MODIFY EXISTING: `app/Exceptions/Handler.php`

**Action:** Replace entire file content  
**Purpose:** Catch all `RecordNotAccessibleException` and `ModelNotFoundException` instances at the framework level. For Livewire 3, dispatches `showAlert` browser event instead of HTML 404 page.

**Complete replacement code:**

```php
<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        // Report custom exceptions with extra context
        $this->reportable(function (RecordNotAccessibleException $e) {
            \Log::warning('RecordNotAccessibleException: ' . $e->getUserMessage(), $e->getContext());
        });

        $this->reportable(function (ModelNotFoundException $e) {
            \Log::warning('ModelNotFoundException caught by safety net', [
                'message' => $e->getMessage(),
                'url'     => request()->fullUrl(),
                'user_id' => auth()->id() ?? 'guest',
            ]);
        });

        // Default reportable for all other exceptions
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * Override to provide user-friendly handling for:
     *  - RecordNotAccessibleException (custom domain exception)
     *  - ModelNotFoundException (safety net for any missed findOrFail calls)
     *
     * @param  Request  $request
     * @param  Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        // ── Handle our custom RecordNotAccessibleException ──────────────────
        if ($e instanceof RecordNotAccessibleException) {
            return $this->renderRecordNotAccessible($request, $e);
        }

        // ── Safety net: catch any remaining ModelNotFoundException ──────────
        //    (from findOrFail calls we may have missed, or from third-party packages)
        if ($e instanceof ModelNotFoundException) {
            return $this->renderModelNotFound($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Render a RecordNotAccessibleException.
     */
    protected function renderRecordNotAccessible(Request $request, RecordNotAccessibleException $e): Response
    {
        $status  = $e->getHttpStatusCode();
        $message = $e->getUserMessage();
        $route   = $e->getRedirectRoute();

        // ── JSON / API / AJAX requests ─────────────────────────────────────
        if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'status'  => $status,
            ], $status);
        }

        // ── Livewire requests: dispatch showAlert event ───────────────────
        //    Livewire 3 uses a X-Livewire header on its AJAX requests.
        //    We return a 200 with an event dispatch so the component can
        //    handle the error inline rather than showing a full error page.
        if ($request->hasHeader('X-Livewire')) {
            return response()->json([
                'effects' => [
                    'dispatches' => [
                        [
                            'name'  => 'showAlert',
                            'params' => [
                                'type'    => 'error',
                                'message' => $message,
                            ],
                        ],
                    ],
                ],
                'redirect' => $route ? route($route) : null,
            ], 200);
        }

        // ── Standard web request ──────────────────────────────────────────
        if ($route) {
            return redirect()
                ->route($route)
                ->with('error', $message);
        }

        // Fallback: return a clean error view if no redirect is configured
        return response()->view('errors.custom', [
            'title'   => $status === 403 ? 'Access Denied' : 'Record Not Found',
            'message' => $message,
        ], $status);
    }

    /**
     * Render a ModelNotFoundException (safety net).
     */
    protected function renderModelNotFound(Request $request, ModelNotFoundException $e): Response
    {
        $message = 'The requested record could not be found. It may have been deleted or the link is invalid.';

        // ── JSON / API / AJAX requests ─────────────────────────────────────
        if ($request->expectsJson() || $request->is('api/*') || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'status'  => 404,
            ], 404);
        }

        // ── Livewire requests ─────────────────────────────────────────────
        if ($request->hasHeader('X-Livewire')) {
            return response()->json([
                'effects' => [
                    'dispatches' => [
                        [
                            'name'   => 'showAlert',
                            'params' => [
                                'type'    => 'error',
                                'message' => $message,
                            ],
                        ],
                    ],
                ],
            ], 200);
        }

        // ── Standard web request ──────────────────────────────────────────
        //    Fall back to Laravel's default 404 handling
        return parent::render($request, $e);
    }
}
```

**Milestone 1:** Infrastructure in place. No `findOrFail` calls modified yet. Safety net ready.

---

## Phase 2: ui-library Generic Components (Highest Leverage)

### 2.1 — MODIFY EXISTING: `/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/DataTables/DataTableDetail.php`

**Risk:** MEDIUM | **Anti-Patterns:** #1 (Dynamic model), #3 (Double findOrFail)  
**Changes:**

1. Add imports at top:
```php
use App\Exceptions\RecordNotAccessibleException;
use App\Traits\ResolvesModels;
```

2. Add trait to class:
```php
class DataTableDetail extends Component
{
    use ResolvesModels;
```

3. Replace `mount()` method (around lines 25-37). **BEFORE:**
```php
public function mount(string $configKey, int $recordId, $inline = false, array $returnParams = [])
{
    $this->configKey = $configKey;
    $this->recordId = $recordId;
    $this->returnParams = $returnParams;
    $this->inline = $inline;

    $this->loadConfiguration();
    $this->loadRecord();                                            // findOrFail #1

    $modelClass = app(ConfigResolver::class, ['configKey' => $this->configKey])->getModel();
    app(AuthorizationService::class)->authorizeView(auth()->user(), $this->recordId, $modelClass);  // findOrFail #2
}
```

**AFTER:**
```php
public function mount(string $configKey, int $recordId, $inline = false, array $returnParams = [])
{
    $this->configKey = $configKey;
    $this->recordId = $recordId;
    $this->returnParams = $returnParams;
    $this->inline = $inline;

    $this->loadConfiguration();

    // Resolve the model ONCE with safe resolution
    $modelClass = $this->getConfigResolver()->getModel();
    $relations  = array_keys($this->getConfigResolver()->getRelations());

    $this->record = $this->resolveModel($modelClass, $recordId, [
        function ($query) use ($relations) {
            return $query->with($relations);
        },
    ]);

    if (!$this->record) {
        throw RecordNotAccessibleException::notFound(
            $modelClass,
            $recordId,
            $this->getFallbackRoute()
        );
    }

    // Authorize using the already-resolved Model instance
    // This avoids the second findOrFail() in AuthorizationService
    app(AuthorizationService::class)->authorizeView(
        auth()->user(),
        $this->record,       // Pass the Model instance, not the ID
        $modelClass
    );
}
```

4. **REMOVE** the old `loadRecord()` method entirely (around lines 64-69):
```php
// DELETE THIS METHOD:
protected function loadRecord(): void
{
    $modelClass = $this->getConfigResolver()->getModel();
    $relations = array_keys($this->getConfigResolver()->getRelations());
    $this->record = $modelClass::with($relations)->findOrFail($this->recordId);
}
```

5. **ADD** new helper method:
```php
/**
 * Get the fallback redirect route name for this detail page.
 * Override in child classes or derive from configKey.
 */
protected function getFallbackRoute(): string
{
    // Derive from configKey: 'employees' → 'employees.index'
    $module = str_replace('_', '-', $this->configKey);
    return $module . '.index';
}
```

---

### 2.2 — MODIFY EXISTING: `/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/DataTables/DataTableForm.php`

**Risk:** MEDIUM | **Anti-Patterns:** #1 (Dynamic model), #3 (Double findOrFail)  
**Changes:**

1. Add imports at top:
```php
use App\Exceptions\RecordNotAccessibleException;
use App\Traits\ResolvesModels;
```

2. Add trait to class:
```php
class DataTableForm extends Component
{
    use ResolvesModels;
```

3. In the `save()` method, replace the transaction block (around line 670-673). **BEFORE:**
```php
DB::transaction(function () {
    $record = $this->isEditMode
        ? $this->modelClass::findOrFail($this->recordId)
        : new $this->modelClass();
    // ... save logic
});
```

**AFTER:**
```php
DB::transaction(function () {
    if ($this->isEditMode) {
        $record = $this->resolveModel($this->modelClass, $this->recordId);

        if (!$record) {
            // Record was deleted between form load and save.
            // Throw so the transaction rolls back and user gets feedback.
            throw RecordNotAccessibleException::notFound(
                $this->modelClass,
                $this->recordId,
                $this->getFallbackRoute()
            );
        }
    } else {
        $record = new $this->modelClass();
    }

    // ... rest of existing save/update logic ...
});
```

4. **ADD** new helper method:
```php
/**
 * Get the fallback redirect route name for this form.
 */
protected function getFallbackRoute(): string
{
    $module = str_replace('_', '-', $this->configKey);
    return $module . '.index';
}
```

---

### 2.3 — MODIFY EXISTING: `/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/Wizards/WizardForm.php`

**Risk:** MEDIUM | **Anti-Pattern:** #1 (Dynamic model)  
**Changes:**

1. Add imports at top:
```php
use App\Exceptions\RecordNotAccessibleException;
use App\Traits\ResolvesModels;
```

2. Add trait to class:
```php
class WizardForm extends Component
{
    use ResolvesModels;
```

3. In the save/finalize method, replace the transaction block (around line 303-308). **BEFORE:**
```php
DB::transaction(function () {
    if ($this->isEditMode) {
        $record = $this->modelClass::findOrFail($this->recordId);
    } else {
        $record = new $this->modelClass();
    }
    // ... save logic
});
```

**AFTER** (wrap in try-catch):
```php
try {
    DB::transaction(function () {
        if ($this->isEditMode) {
            $record = $this->resolveModel($this->modelClass, $this->recordId);

            if (!$record) {
                // Record not found — clean up wizard session and alert user.
                // We cannot redirect inside a DB::transaction callback,
                // so we throw and let the catch block handle it.
                throw RecordNotAccessibleException::notFound(
                    $this->modelClass,
                    $this->recordId
                );
            }
        } else {
            $record = new $this->modelClass();
        }

        // ... existing save logic ...
    });
} catch (RecordNotAccessibleException $e) {
    // Clean up wizard session state
    $wizardId = $this->getWizardId();
    if ($wizardId && session()->has($wizardId)) {
        session()->forget($wizardId);
    }

    // Dispatch error to the Livewire UI
    $this->dispatch('showAlert', [
        'type'    => 'error',
        'message' => $e->getUserMessage(),
    ]);

    // Redirect to fallback
    return $this->redirectRoute($this->getFallbackRoute());
}
```

---

### 2.4 — MODIFY EXISTING: `/Users/mac/Projects/Libraries/ui-library/src/Http/Livewire/Custom/EmployeeDetail.php`

**Risk:** MEDIUM | **Anti-Pattern:** #4 (Session-scope 404)  
**Changes:**

1. Add imports at top:
```php
use App\Traits\ResolvesModels;
```

2. Add trait to class:
```php
class EmployeeDetail extends Component
{
    use ResolvesModels;
```

3. In `mount()` or `loadEmployee()`, replace the `findOrFail` chain (around lines 136-147). **BEFORE:**
```php
$this->employee = $modelClass::with([
    'employeeProfile',
    'employeePosition.jobTitle',
    'employeePosition.department',
    'employeePosition.manager',
    'employeePosition.reportsTo',
    'employeePosition.location',
    'employeePosition.shift',
    'employeePosition.attendancePolicy',
    'jobHistory',
    'employeeWorkPatterns.workPattern',
])->findOrFail($this->recordId);
```

**AFTER:**
```php
$employee = $this->resolveModel(Employee::class, $this->recordId, [
    function ($query) {
        return $query->with([
            'employeeProfile',
            'employeePosition.jobTitle',
            'employeePosition.department',
            'employeePosition.manager',
            'employeePosition.reportsTo',
            'employeePosition.location',
            'employeePosition.shift',
            'employeePosition.attendancePolicy',
            'jobHistory',
            'employeeWorkPatterns.workPattern',
        ]);
    },
]);

if (!$employee) {
    $this->flashAndRedirect(
        'error',
        'Employee not found. They may have been removed from the system.',
        'employees.index'
    );
    return;
}

// Check company access
if (!$this->checkCompanyAccess($employee)) {
    $this->flashAndRedirect(
        'error',
        'This employee belongs to a different company and is not accessible from your current view. Please switch companies.',
        'employees.index'
    );
    return;
}

$this->employee = $employee;
```

**Milestone 2:** All generic components safe. ~50% of risk eliminated.

---

## Phase 3: HIGH-Risk Items (Critical Security/Stability)

### 3.1 — MODIFY EXISTING: `app/Modules/Admin/Services/AuthorizationService.php`

**Risk:** HIGH | **Anti-Pattern:** #1 (Dynamic model) | **Line:** 399  
**Changes:**

1. Add import at top:
```php
use App\Exceptions\RecordNotAccessibleException;
```

2. Replace `resolveRecord()` method (around line 393-402). **BEFORE:**
```php
private function resolveRecord($recordOrId, ?string $modelClass = null): Model
{
    if ($recordOrId instanceof Model) {
        return $recordOrId;
    }
    if (is_int($recordOrId) && $modelClass && class_exists($modelClass)) {
        return $modelClass::findOrFail($recordOrId);
    }
    throw new \InvalidArgumentException('Invalid record or ID/class combination.');
}
```

**AFTER:**
```php
private function resolveRecord($recordOrId, ?string $modelClass = null): Model
{
    if ($recordOrId instanceof Model) {
        return $recordOrId;
    }

    if (is_int($recordOrId) && $recordOrId > 0 && $modelClass && class_exists($modelClass)) {
        // Use withoutCompanyScope() because this is an authorization service.
        // The concern is "does this record exist?" — not "does it match
        // the current session company?" Company access is handled separately.
        $record = $modelClass::withoutCompanyScope()->find($recordOrId);

        if (!$record) {
            throw RecordNotAccessibleException::notFound(
                $modelClass,
                $recordOrId
            );
        }

        return $record;
    }

    throw new \InvalidArgumentException(
        sprintf(
            'Invalid record or ID/class combination. ID: %s, Class: %s',
            var_export($recordOrId, true),
            $modelClass ?? 'null'
        )
    );
}
```

3. Update the calling `authorize()` method (around line 274) to catch the exception. **BEFORE:**
```php
private function authorize(User $user, $recordOrId, ?string $modelClass, string $permissionType): void
{
    $record = $this->resolveRecord($recordOrId, $modelClass);
    // ... rest of authorization logic
}
```

**AFTER:**
```php
private function authorize(User $user, $recordOrId, ?string $modelClass, string $permissionType): void
{
    try {
        $record = $this->resolveRecord($recordOrId, $modelClass);
    } catch (RecordNotAccessibleException $e) {
        abort($e->getHttpStatusCode(), $e->getUserMessage());
    }

    // ... rest of authorization logic
}
```

---

### 3.2 — MODIFY EXISTING: `app/Modules/Hr/Resources/views/attendance-work-sessions.blade.php`

**Risk:** HIGH | **Anti-Pattern:** #2 (Blade DB query) | **Line:** 7

**Option A (Recommended):** Create/modify a controller method.

Find or create the controller that serves this view. Add this method:

```php
// In the appropriate controller (e.g., AttendanceController.php):
public function workSessions(Request $request)
{
    $attendanceId = $request->get('attendance_id');

    if (!$attendanceId) {
        abort(404, 'Attendance ID is required to view work sessions.');
    }

    $attendance = Attendance::where('id', $attendanceId)->first();

    if (!$attendance || !$attendance->employee_id) {
        abort(404, 'Attendance record not found or has no associated employee.');
    }

    $employee = Employee::find($attendance->employee_id);

    if (!$employee) {
        abort(404, 'The employee associated with this attendance record could not be found.');
    }

    $subPageTitle = 'For ' . $employee->first_name . ' ' . $employee->last_name
        . ' (' . $employee->employee_id . ')';

    return view('hr::attendance-work-sessions', compact('attendanceId', 'subPageTitle'));
}
```

Then in the blade view, replace the `@php` block (lines 4-9). **BEFORE:**
```blade
@php
    $attensance_id = request()->get('attendance_id') ?? null;
    $employeeId = \App\Modules\Hr\Models\Attendance::where('id', $attensance_id)->first()?->employee_id;
    $employee = \App\Modules\Hr\Models\Employee::findOrFail($employeeId);
    $subPageTitle = 'For ' . $employee->first_name . ' ' . $employee->last_name . ' (' . $employeeId . ')';
@endphp
```

**AFTER:**
```blade
{{-- All logic moved to controller; variables are passed via compact() --}}
{{-- $attendanceId and $subPageTitle are available --}}
```

**Option B (Quick blade-only fix — temporary):** Replace the `@php` block with:
```blade
@php
    $attendance_id = request()->get('attendance_id');
    $employee = null;
    $subPageTitle = 'Work Sessions';
    
    if ($attendance_id) {
        $attendance = \App\Modules\Hr\Models\Attendance::where('id', $attendance_id)->first();
        if ($attendance && $attendance->employee_id) {
            $employee = \App\Modules\Hr\Models\Employee::find($attendance->employee_id);
        }
    }
    
    if ($employee) {
        $subPageTitle = 'For ' . $employee->first_name . ' ' . $employee->last_name . ' (' . $employee->employee_id . ')';
    } else {
        abort(404, 'Attendance record or associated employee not found.');
    }
@endphp
```

---

### 3.3 — MODIFY EXISTING: `/Users/mac/Projects/Libraries/ui-library/src/Http/Controllers/Prints/GenericDetailPagePrintController.php`

**Risk:** HIGH | **Anti-Pattern:** #1 (Dynamic model + no auth) | **Line:** 33  
**Changes:**

1. Add imports at top:
```php
use App\Traits\ResolvesModels;
```

2. Add trait to class:
```php
class GenericDetailPagePrintController extends Controller
{
    use ResolvesModels;
```

3. Replace `show()` method. **BEFORE:**
```php
public function show($configKey, $id)
{
    $resolver = app(ConfigResolver::class, ['configKey' => $configKey]);
    $modelClass = $resolver->getModel();
    $record = $modelClass::findOrFail($id);
    // ... render print view
}
```

**AFTER:**
```php
public function show($configKey, $id)
{
    // Validate configKey resolves to a valid class
    try {
        $resolver = app(ConfigResolver::class, ['configKey' => $configKey]);
        $modelClass = $resolver->getModel();
    } catch (\Exception $e) {
        abort(404, 'Print configuration not found.');
    }

    // Validate ID is numeric and positive
    if (!is_numeric($id) || (int) $id <= 0) {
        abort(404, 'Invalid record identifier.');
    }

    // Safe resolution
    $record = $this->resolveModel($modelClass, (int) $id);

    if (!$record) {
        abort(404, 'The record you are trying to print could not be found.');
    }

    // Authorization check — ensure the user can view this record
    if (method_exists($record, 'getPolicy') || \Gate::has('view', $modelClass)) {
        $this->authorize('view', $record);
    }

    // ... rest of the existing print logic
    $viewName = $resolver->getPrintView();
    $data = $resolver->getPrintData($record);

    return view($viewName, $data);
}
```

---

### 3.4 — MODIFY EXISTING: ui-library `Routes/web.php` (print route)

**Risk:** HIGH | **Action:** Wrap print route in `auth` middleware

**BEFORE:**
```php
Route::get('/print/{configKey}/{id}', [GenericDetailPagePrintController::class, 'show'])
    ->name('generic.print');
```

**AFTER:**
```php
Route::middleware(['auth'])->group(function () {
    Route::get('/print/{configKey}/{id}', [GenericDetailPagePrintController::class, 'show'])
        ->name('generic.print');
});
```

---

### 3.5 — MODIFY EXISTING: `/Users/mac/Projects/Libraries/ui-library/src/Http/Controllers/Imports/ImportController.php`

**Risk:** HIGH | **Anti-Pattern:** #5 (No user scoping) | **Line:** 26  
**Changes:** Replace `status()` method.

**BEFORE:**
```php
public function status($id)
{
    $import = Import::findOrFail($id);
    $completedChunks = ImportChunk::where('import_id', $import->id)
        ->whereIn('status', ['completed', 'failed'])
        ->count();
    $totalChunks = $import->total_chunks ?? 0;
    // ... returns JSON
}
```

**AFTER:**
```php
public function status($id)
{
    // Scope to the authenticated user
    $import = Import::where('id', $id)
        ->where('user_id', auth()->id())
        ->first();

    if (!$import) {
        return response()->json([
            'status' => 'not_found',
            'error'  => 'Import not found or you do not have access to it.',
        ], 404);
    }

    $completedChunks = ImportChunk::where('import_id', $import->id)
        ->whereIn('status', ['completed', 'failed'])
        ->count();
    $totalChunks = $import->total_chunks ?? 0;

    // Build error file URL if available
    $errorFileUrl = null;
    if ($import->error_file) {
        $errorFileUrl = route('import.download-errors', ['import' => $import->id]);
    }

    return response()->json([
        'status'            => $import->status,
        'total_rows'        => $import->total_rows ?? 0,
        'successful_rows'   => $import->successful_rows ?? 0,
        'failed_rows'       => $import->failed_rows ?? 0,
        'completed_chunks'  => $completedChunks,
        'total_chunks'      => $totalChunks,
        'error_file_url'    => $errorFileUrl,
        'error_summary'     => $import->error_summary ?? null,
    ]);
}
```

---

### 3.6 — MODIFY EXISTING: `/Users/mac/Projects/Libraries/ui-library/src/Http/Controllers/Exports/ExportController.php`

**Risk:** HIGH | **Anti-Pattern:** #5 (No user scoping) | **Line:** 217  
**Changes:** Replace `exportStatus()` method.

**BEFORE:**
```php
public function exportStatus($id)
{
    $export = Export::findOrFail($id);
    $fileUrl = $export->status === 'completed' && $export->download_token
        ? route('export.download', ['token' => $export->download_token])
        : null;
    // ... returns JSON
}
```

**AFTER:**
```php
public function exportStatus($id)
{
    // Scope to the authenticated user
    $export = Export::where('id', $id)
        ->where('user_id', auth()->id())
        ->first();

    if (!$export) {
        return response()->json([
            'status' => 'not_found',
            'error'  => 'Export not found or you do not have access to it.',
        ], 404);
    }

    $fileUrl = null;
    if ($export->status === 'completed' && $export->download_token) {
        $fileUrl = route('export.download', ['token' => $export->download_token]);
    }

    // Calculate chunk progress
    $completedChunks = 0;
    $totalChunks = $export->total_chunks ?? 0;
    if ($totalChunks > 0 && in_array($export->status, ['processing', 'pending', 'completed'])) {
        $completedChunks = ExportChunk::where('export_id', $export->id)->count();
    }

    return response()->json([
        'status'           => $export->status,
        'file_url'         => $fileUrl,
        'file_size'        => $export->file_size,
        'error'            => $export->error_message,
        'completed_at'     => $export->completed_at,
        'completed_chunks' => $completedChunks,
        'total_chunks'     => $totalChunks,
    ]);
}
```

---

### 3.7 — MODIFY EXISTING: `app/Modules/Hr/Services/Payroll/PayrollCalculator.php`

**Risk:** HIGH | **Anti-Pattern:** #2 (Null-prop before `??`) | **Line:** 754  
**Changes:** Replace `processCompany()` method around lines 749-756.

**BEFORE:**
```php
protected function processCompany(
    int $companyId,
    Collection $positions,
    int $totalCompanies
): array {
    $company = Company::find($companyId);
    $companyName = $company->name ?? 'Unknown';
    // ... rest of processing
}
```

**AFTER:**
```php
protected function processCompany(
    int $companyId,
    Collection $positions,
    int $totalCompanies
): array {
    $company = Company::find($companyId);

    if (!$company) {
        \Log::warning("PayrollCalculator: Company #{$companyId} not found during payroll run", [
            'run_id'    => $this->run->id ?? null,
            'company_id' => $companyId,
        ]);

        return [
            'company_id'      => $companyId,
            'company_name'    => 'Unknown Company (deleted)',
            'employee_count'  => 0,
            'processed_count' => 0,
            'failed_count'    => 0,
            'status'          => 'skipped',
            'employees'       => [],
            'errors'          => ['Company not found in database. It may have been deleted during processing.'],
        ];
    }

    $companyName = $company->name;

    // ... rest of the existing processing logic
}
```

**Milestone 3:** All HIGH-risk items addressed. No data leaks, no crashes.

---

## Phase 4: MEDIUM-Risk Items (Stability)

### 4.1 — MODIFY EXISTING: `app/Modules/Hr/Http/Livewire/Payroll/PayrollRunWizard.php`

**Risk:** MEDIUM | **Anti-Pattern:** #4 (Session-scope 404) | **Line:** 75  
**Changes:**

1. Add imports at top:
```php
use App\Traits\ResolvesModels;
```

2. Add trait to class:
```php
class PayrollRunWizard extends Component
{
    use ResolvesModels;
```

3. In `mount()`, replace the `findOrFail` block (around line 74-76). **BEFORE:**
```php
} elseif ($payrollRunId) {
    $run = PayrollRun::findOrFail($payrollRunId);
    $this->payrollRunId = $run->id;
```

**AFTER:**
```php
} elseif ($payrollRunId) {
    $run = $this->resolveModel(PayrollRun::class, $payrollRunId);

    if (!$run) {
        $this->cleanupWizardSession(
            $this->getWizardId(),
            'The payroll run you are trying to edit could not be found. It may have been deleted.',
            'payroll-runs.index'
        );
        return;
    }

    $this->payrollRunId = $run->id;
    // ... rest of initialization
}
```

---

### 4.2 — MODIFY EXISTING: `app/Modules/Hr/Http/Livewire/Payroll/PayrollRunDetail.php`

**Risk:** MEDIUM | **Anti-Pattern:** #4 (Session-scope 404) | **Line:** 41  
**Changes:**

1. Add imports at top:
```php
use App\Traits\ResolvesModels;
```

2. Add trait to class:
```php
class PayrollRunDetail extends Component
{
    use ResolvesModels;
```

3. Replace `mount()` method (around lines 36-43). **BEFORE:**
```php
public function mount(int $recordId, string $configKey, array $returnParams = []): void
{
    $this->recordId = $recordId;
    $this->configKey = $configKey;
    $this->returnParams = $returnParams;
    $this->run = PayrollRun::with(['paySchedule'])->findOrFail($recordId);
    $this->tabs = $this->getTabs();
}
```

**AFTER:**
```php
public function mount(int $recordId, string $configKey, array $returnParams = []): void
{
    $this->recordId = $recordId;
    $this->configKey = $configKey;
    $this->returnParams = $returnParams;

    $run = $this->resolveModel(PayrollRun::class, $recordId, [
        function ($query) {
            return $query->with(['paySchedule']);
        },
    ]);

    if (!$run) {
        $this->flashAndRedirect(
            'error',
            'Payroll run not found. It may have been deleted or is not accessible from your current view.',
            'payroll-runs.index'
        );
        return;
    }

    $this->run = $run;
    $this->tabs = $this->getTabs();
}
```

---

### 4.3 — MODIFY EXISTING: `app/Modules/Hr/Listeners/AttendanceEventListener.php`

**Risk:** MEDIUM | **Line:** 74  
**Changes:** Add specific `ModelNotFoundException` catch before the generic `\Exception` catch (around line 132).

**BEFORE:**
```php
} catch (\Exception $e) {
    DB::rollBack();
    // ... generic error handling
}
```

**AFTER** (insert `ModelNotFoundException` catch BEFORE the generic catch):
```php
} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    DB::rollBack();

    \Log::warning('AttendanceEventListener: Record not found during recalculation', [
        'attendance_id' => $attendanceId,
    ]);

    SweetAlertService::showError(
        $livewireComponent,
        'Error!',
        'The attendance record could not be found. It may have been deleted by another user.'
    );

} catch (\Exception $e) {
    DB::rollBack();

    \Log::error('Attendance recalculation failed', [
        'attendance_id' => $attendanceId,
        'error'         => $e->getMessage(),
        'trace'         => $e->getTraceAsString(),
    ]);

    SweetAlertService::showError(
        $livewireComponent,
        'Error!',
        'Recalculation failed. Please try again or contact support if the issue persists.'
    );
}
```

---

### 4.4 — MODIFY EXISTING: `app/Modules/Hr/Http/Livewire/Payroll/PayrollWizardAdjustments.php`

**Risk:** MEDIUM | **Anti-Pattern:** #2 (Null-prop before `??`) | **Line:** 362  
**Changes:** Fix `getCurrentCompanyNameProperty()` method.

**BEFORE:**
```php
public function getCurrentCompanyNameProperty(): string
{
    $companyId = session('current_company_id');
    if (!$companyId || $companyId === 0) {
        return 'All Companies';
    }
    $company = \App\Modules\Admin\Models\Company::find($companyId);
    return $company->name ?? 'Unknown Company';  // ← BUG
}
```

**AFTER:**
```php
public function getCurrentCompanyNameProperty(): string
{
    $companyId = session('current_company_id');
    if (!$companyId || $companyId === 0) {
        return 'All Companies';
    }
    $company = \App\Modules\Admin\Models\Company::find($companyId);
    return $company?->name ?? 'Unknown Company';  // PHP 8 null-safe operator
}
```

---

### 4.5 — MODIFY EXISTING: `app/Modules/Hr/Http/Livewire/Payroll/PayrollWizardPreview.php`

**Risk:** MEDIUM | **Anti-Pattern:** #2 (Null-prop before `??`) | **Line:** 408  
**Changes:** Fix `getCurrentCompanyNameProperty()` method.

**BEFORE:**
```php
public function getCurrentCompanyNameProperty(): string
{
    $companyId = session('current_company_id');
    if (!$companyId || $companyId === 0) {
        return 'All Companies';
    }
    $company = \App\Modules\Admin\Models\Company::find($companyId);
    return $company->name ?? 'Unknown Company';  // ← BUG
}
```

**AFTER:**
```php
public function getCurrentCompanyNameProperty(): string
{
    $companyId = session('current_company_id');
    if (!$companyId || $companyId === 0) {
        return 'All Companies';
    }
    $company = \App\Modules\Admin\Models\Company::find($companyId);
    return $company?->name ?? 'Unknown Company';  // PHP 8 null-safe operator
}
```

**Optional improvement:** Extract the duplicated `getCurrentCompanyNameProperty()` into a shared helper. Add to `ResolvesModels` trait:

```php
/**
 * Get the display name for the current session company.
 */
protected function getCurrentCompanyDisplayName(): string
{
    $companyId = session('current_company_id');

    if (empty($companyId) || (int) $companyId === 0) {
        return 'All Companies';
    }

    $company = \App\Modules\Admin\Models\Company::find($companyId);

    return $company?->name ?? 'Unknown Company';
}
```

Then both components call `$this->getCurrentCompanyDisplayName()` instead of duplicating logic.

**Milestone 4:** All MEDIUM-risk items addressed. 33/33 occurrences resolved.

---

## Phase 5: Verification

- [ ] Run full test suite: `php artisan test`
- [ ] Run raw-findings scan to confirm 0 remaining `findOrFail`/`firstOrFail` issues
- [ ] Manual testing of all modified flows (see test scenarios below)
- [ ] Code review by at least one other developer

### Manual Test Scenarios

| # | Scenario | Steps | Expected Result |
|---|----------|-------|-----------------|
| 1 | Invalid ID in data table detail | Navigate to `/hr/employees/99999` | Flash error "Employee not found" + redirect to employees.index |
| 2 | Import status polling after delete | Start import, note import ID, delete from DB, wait for poll | JSON `{"status":"not_found","error":"..."}` with HTTP 200 |
| 3 | Export status polling after delete | Same as above for export | JSON `{"status":"not_found","error":"..."}` with HTTP 200 |
| 4 | Company switcher changes while viewing detail | View employee from company A, switch to company B | Graceful redirect: "Employee belongs to a different company" |
| 5 | Wizard with deleted backing record | Open payroll wizard, delete the payroll run in another tab, click Next | "Record not found" + redirect to index + wizard session cleared |
| 6 | Print endpoint without auth | Navigate to `/print/employees/1` without logging in | Redirected to login page (auth middleware) |
| 7 | Print endpoint with invalid configKey | Navigate to `/print/nonexistent/1` as logged-in user | "Print configuration not found" 404 page |
| 8 | Edit form save after record deleted | Open edit form for record #42, delete #42 in another tab, click Save | "Record not found — it may have been deleted" showAlert + redirect |
| 9 | Attendance work sessions with missing attendance_id | Navigate to `/hr/attendance-work-sessions` (no query param) | "Attendance ID is required" 404 page |
| 10 | Payroll calculator with deleted company | Run multi-company payroll, delete one company mid-run | Warning logged, company skipped, payroll continues |
| 11 | Payroll wizard from stale company session | Edit payroll run, change company, re-render | Clean redirect with "may have been deleted" message |
| 12 | Cross-user import status polling | User A polls `/import/status/{UserB-import-id}` | "Import not found or you do not have access" JSON 404 |
| 13 | Livewire component catches ModelNotFoundException safety net | Any missed findOrFail in a Livewire request | `showAlert` event dispatched with error message, no HTML 404 page |

---

## Appendix: Complete File Change Summary

### New Files (2)

| # | File | Phase |
|---|------|-------|
| 1 | `app/Traits/ResolvesModels.php` | Phase 1 |
| 2 | `app/Exceptions/RecordNotAccessibleException.php` | Phase 1 |

### Modified Files (17)

| # | File | Phase | Risk |
|---|------|-------|------|
| 1 | `app/Exceptions/Handler.php` | Phase 1 | — |
| 2 | ui-library `DataTableDetail.php` | Phase 2 | MEDIUM |
| 3 | ui-library `DataTableForm.php` | Phase 2 | MEDIUM |
| 4 | ui-library `WizardForm.php` | Phase 2 | MEDIUM |
| 5 | ui-library `EmployeeDetail.php` | Phase 2 | MEDIUM |
| 6 | `app/Modules/Admin/Services/AuthorizationService.php` | Phase 3 | HIGH |
| 7 | `app/Modules/Hr/Resources/views/attendance-work-sessions.blade.php` | Phase 3 | HIGH |
| 8 | ui-library `GenericDetailPagePrintController.php` | Phase 3 | HIGH |
| 9 | ui-library `Routes/web.php` (print route) | Phase 3 | HIGH |
| 10 | ui-library `ImportController.php` | Phase 3 | HIGH |
| 11 | ui-library `ExportController.php` | Phase 3 | HIGH |
| 12 | `app/Modules/Hr/Services/Payroll/PayrollCalculator.php` | Phase 3 | HIGH |
| 13 | `app/Modules/Hr/Http/Livewire/Payroll/PayrollRunWizard.php` | Phase 4 | MEDIUM |
| 14 | `app/Modules/Hr/Http/Livewire/Payroll/PayrollRunDetail.php` | Phase 4 | MEDIUM |
| 15 | `app/Modules/Hr/Listeners/AttendanceEventListener.php` | Phase 4 | MEDIUM |
| 16 | `app/Modules/Hr/Http/Livewire/Payroll/PayrollWizardAdjustments.php` | Phase 4 | MEDIUM |
| 17 | `app/Modules/Hr/Http/Livewire/Payroll/PayrollWizardPreview.php` | Phase 4 | MEDIUM |

### Files NOT Changed (19 — LOW risk, already safe)

Items #5, #7, #8, #9, #11, #13, #14, #15, #16, #17, #18, #20, #23, #24, #25, #28, #29, #33 — all already properly guarded with null checks, user scoping, or `optional()` wrapping.

---

## Rollback Plan

Each phase is independently revertible:

| Phase | Rollback Action | Impact |
|-------|----------------|--------|
| Phase 1 (Infrastructure) | Delete 3 new files; revert `Handler.php` | Zero impact — no behavior change without Phase 2+ |
| Phase 2 (ui-library) | Revert 4 component files | Returns to original `findOrFail()` behavior |
| Phase 3 (HIGH-risk) | Revert 7 files | Returns to original behavior |
| Phase 4 (MEDIUM-risk) | Revert 5 files | Returns to original behavior |

**Emergency hotfix:** The `ResolvesModels` trait is additive — no existing code was deleted. Simply restore the original `findOrFail()` call on the affected line. The trait remains in place but unused — no side effects.
