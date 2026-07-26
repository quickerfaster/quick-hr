<?php

namespace App\Modules\Admin\Services;

use App\Modules\Admin\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthorizationService
{
    // Constants for view access control
    const ROLE_ADMIN_ONLY_VIEWS = [
        'user-role-management',
        'user-role-assignment',
        'access-control-management',
        'role-assignment'
    ];

    const CUSTOM_VIEW_MODEL_NAMES = [
        'user-role-management' => 'role',
        'assign-user-roles' => 'role',
        'access-control-management' => 'role',
        'employee-onboarding' => 'employee', // employee permission needed
        'payroll-wizard' => 'payroll_run', // payroll_run permission needed
    ];

    const ADMIN_ROLES = ['super_admin', 'company_admin'];

    /**
     * Check if user is super admin or company admin (bypass all granular checks).
     */
    private function isBypassAllowed($user): bool
    {
        return $user && $user->hasAnyRole(self::ADMIN_ROLES);
    }

    /**
     * Check if a user can access a given view (page).
     *
     * @param User|null $user
     * @param string $view
     * @return bool
     */
    public function canAccessView($user, string $view): bool
    {
        if (!$user) {
            return false;
        }

        // Bypass for super admin / company admin
        if ($this->isBypassAllowed($user)) {
            return true;
        }

        // Admin-only views (for regular admins, not bypassed)
        if (in_array($view, self::ROLE_ADMIN_ONLY_VIEWS)) {
            return $user->hasAnyRole(self::ADMIN_ROLES);
        }

        // Public views that don't require permission or belongs to the user
        if (in_array($view, ['dashboard', 'my-profile', 'my-account', 'my-preferences'])) {
            return true;
        }

        // For non-admins, check specific view permission
        if (!$user->hasAnyRole(self::ADMIN_ROLES)) {
            $permission = 'view_' . $this->getViewModelName($view);
            return $user->can($permission);
        }

        return false;
    }

    /**
     * Convert a view name to the corresponding permission model name.
     */
    private function getViewModelName(string $view): string
    {
        if (array_key_exists($view, self::CUSTOM_VIEW_MODEL_NAMES)) {
            return self::CUSTOM_VIEW_MODEL_NAMES[$view];
        }

        if (str_starts_with($view, 'dashboard-')) {
            $view = str_replace("dashboard-", "", $view);
        }

        $view = str_replace('-', '_', $view);
        return Str::singular($view);
    }

    /**
     * Check if user can perform an action on a specific row (record).
     * Conditions (state-based) are always enforced, even for admins.
     * Roles/permissions are bypassed for super_admin and company_admin.
     *
     * @param User $user
     * @param array $action  e.g. ['requiredPermission' => 'update_role']
     * @param Model $row
     * @return bool
     */
    public function canPerformAction(User $user, array $action, $row): bool
    {
        // 1. Check business conditions (state-based) – always apply, even for admins
        if (isset($action['condition'])) {
            $conditions = $action['condition'];
            if (!$this->evaluateConditions($row, $conditions)) {
                return false;
            }
        }

        // 2. Bypass for super admin / company admin (skip role/permission checks)
        if ($this->isBypassAllowed($user)) {
            return true;
        }

        // 3. Check required role
        if (isset($action['requiredRole'])) {
            $requiredRoles = (array) $action['requiredRole'];
            if (!$user->hasAnyRole($requiredRoles)) {
                return false;
            }
        }

        // 4. Check required permission
        if (isset($action['requiredPermission'])) {
            $requiredPermissions = (array) $action['requiredPermission'];
            if (!$user->hasAnyPermission($requiredPermissions)) {
                return false;
            }
        }

        // 5. Check data scope (multi-tenant / user-level)
        if (!$this->isInUserScope($user, $row)) {
            return false;
        }

        return true;
    }

/**
 * Evaluate business conditions on a row.
 * Supports nested groups: if multiple groups are provided, at least one group must match (OR).
 * Within a group, all conditions must match (AND).
 *
 * @param mixed $row
 * @param array $conditions
 * @return bool
 */
public function evaluateConditions($row, array $conditions): bool
{
    if (empty($conditions)) {
        return true;
    }

    // Normalise to an array of groups (each group is an associative array of field => allowedValues)
    if (!isset($conditions[0]) || !is_array($conditions[0])) {
        $conditions = [$conditions];
    }

    foreach ($conditions as $group) {
        $groupPass = true;
        foreach ($group as $field => $allowedValues) {
            // Resolve the actual value from the row
            $actual = null;

            // 1. Check if it's a direct property
            if (property_exists($row, $field)) {
                $actual = $row->$field;
            }
            // 2. Check if it's a method (e.g., trashed())
            elseif (method_exists($row, $field)) {
                $result = $row->$field();
                // If it's a relation, we cannot use it directly; treat as not matching
                if ($result instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
                    // Optionally, you could check if the relation exists (e.g., $row->relation()->exists())
                    // But for simple cases like 'trashed', it returns a boolean, not a relation.
                    // We'll set $actual to null to avoid matching.
                    $actual = null;
                } else {
                    $actual = $result;
                }
            }
            // 3. Fallback to data_get (handles nested relations like 'company.name')
            else {
                $actual = data_get($row, $field);
            }

            // Evaluate against allowed values
            if (is_array($allowedValues)) {
                if (!in_array($actual, $allowedValues, true)) {
                    $groupPass = false;
                    break;
                }
            } else {
                if ($actual != $allowedValues) {
                    $groupPass = false;
                    break;
                }
            }
        }
        if ($groupPass) {
            return true; // at least one group matches
        }
    }

    return false;
}

    // -------------------------------------------------------------------------
    // Bulk permission methods (unchanged)
    // -------------------------------------------------------------------------

    public function canBulkDelete($user, string $modelClass): bool
    {
        if (!$user) return false;
        if ($this->isBypassAllowed($user)) return true;
        $modelName = $this->getModelNameFromClassName($modelClass);
        return $user->can('delete_' . $modelName);
    }

    public function canBulkRestore($user, string $modelClass): bool
    {
        if (!$user) return false;
        if ($this->isBypassAllowed($user)) return true;
        $modelName = $this->getModelNameFromClassName($modelClass);
        return $user->can('restore_' . $modelName);
    }

    public function canBulkForceDelete($user, string $modelClass): bool
    {
        if (!$user) return false;
        if ($this->isBypassAllowed($user)) return true;
        $modelName = $this->getModelNameFromClassName($modelClass);
        return $user->can('force_delete_' . $modelName);
    }

    public function canBulkExport($user, string $modelClass): bool
    {
        if (!$user) return false;
        if ($this->isBypassAllowed($user)) return true;
        $modelName = $this->getModelNameFromClassName($modelClass);
        return $user->can('export_' . $modelName);
    }

    public function canBulkUpdate($user, string $modelClass): bool
    {
        if (!$user) return false;
        if ($this->isBypassAllowed($user)) return true;
        $modelName = $this->getModelNameFromClassName($modelClass);
        return $user->can('edit_' . $modelName);
    }

    // -------------------------------------------------------------------------
    // Generic authorization (aborting) – unchanged
    // -------------------------------------------------------------------------

    public function authorize($user, string $action, $recordOrId = null, ?string $modelClass = null): void
    {
        if (!$user) {
            abort(403, 'Unauthenticated.');
        }

        if ($action === 'create') {
            if (!$modelClass) {
                throw new \InvalidArgumentException('Model class is required for create authorization.');
            }
            if (!$this->canCreate($user, $modelClass)) {
                abort(403, "Unauthorized to create a {$this->getModelNameFromClassName($modelClass)}.");
            }
            return;
        }

        $record = $this->resolveRecord($recordOrId, $modelClass);
        $can = match ($action) {
            'view' => $this->canView($user, $record),
            'edit', 'update' => $this->canUpdate($user, $record),
            'delete' => $this->canDelete($user, $record),
            default => false,
        };

        if (!$can) {
            $modelName = $this->getModelNameFromRecord($record);
            abort(403, "Unauthorized to {$action} this {$modelName}.");
        }
    }

    public function authorizeView($user, $recordOrId, ?string $modelClass = null): void
    {
        $this->authorize($user, 'view', $recordOrId, $modelClass);
    }

    public function authorizeUpdate($user, $recordOrId, ?string $modelClass = null): void
    {
        $this->authorize($user, 'edit', $recordOrId, $modelClass);
    }

    public function authorizeEdit($user, $recordOrId, ?string $modelClass = null): void
    {
        $this->authorize($user, 'edit', $recordOrId, $modelClass);
    }

    public function authorizeCreate($user, string $modelClass): void
    {
        $this->authorize($user, 'create', null, $modelClass);
    }

    public function authorizeDelete($user, $recordOrId, ?string $modelClass = null): void
    {
        $this->authorize($user, 'delete', $recordOrId, $modelClass);
    }

    // -------------------------------------------------------------------------
    // Boolean "can" methods (non‑aborting) – unchanged
    // -------------------------------------------------------------------------

    public function canView($user, $record): bool
    {
        if (!$user) return false;
        if ($this->isBypassAllowed($user)) return true;
        $action = ['requiredPermission' => 'view_' . $this->getModelNameFromRecord($record)];
        return $this->canPerformAction($user, $action, $record);
    }

    public function canUpdate($user, $record): bool
    {
        if (!$user) return false;
        if ($this->isBypassAllowed($user)) return true;
        $action = ['requiredPermission' => 'edit_' . $this->getModelNameFromRecord($record)];
        return $this->canPerformAction($user, $action, $record);
    }

    public function canDelete($user, $record): bool
    {
        if (!$user) return false;
        if ($this->isBypassAllowed($user)) return true;
        $action = ['requiredPermission' => 'delete_' . $this->getModelNameFromRecord($record)];
        return $this->canPerformAction($user, $action, $record);
    }

    public function canCreate($user, string $modelClass): bool
    {
        if (!$user) return false;
        if ($this->isBypassAllowed($user)) return true;
        $modelName = $this->getModelNameFromClassName($modelClass);
        return $user->can('create_' . $modelName);
    }

    public function canExport($user, string $modelClass): bool
    {
        if (!$user) return false;
        if ($this->isBypassAllowed($user)) return true;
        $modelName = $this->getModelNameFromClassName($modelClass);
        return $user->can('export_' . $modelName);
    }

    public function canImport($user, string $modelClass): bool
    {
        if (!$user) return false;
        if ($this->isBypassAllowed($user)) return true;
        $modelName = $this->getModelNameFromClassName($modelClass);
        return $user->can('import_' . $modelName);
    }

    public function canPrint($user, string $modelClass): bool
    {
        if (!$user) return false;
        if ($this->isBypassAllowed($user)) return true;
        $modelName = $this->getModelNameFromClassName($modelClass);
        return $user->can('print_' . $modelName);
    }

    public function canRestore($user, string $modelClass): bool
    {
        if (!$user) return false;
        if ($this->isBypassAllowed($user)) return true;
        $modelName = $this->getModelNameFromClassName($modelClass);
        return $user->can('restore_' . $modelName);
    }

    public function canForceDelete($user, string $modelClass): bool
    {
        if (!$user) return false;
        if ($this->isBypassAllowed($user)) return true;
        $modelName = $this->getModelNameFromClassName($modelClass);
        return $user->can('forceDelete_' . $modelName);
    }

    // -------------------------------------------------------------------------
    // Private Helpers
    // -------------------------------------------------------------------------

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

    private function getModelNameFromRecord(Model $record): string
    {
        return Str::snake(class_basename($record));
    }

    private function getModelNameFromClassName(string $className): string
    {
        return Str::snake(class_basename($className));
    }

    private function getUserCompanyId(User $user): ?int
    {
        if ($user->hasRole('super_admin')) {
            return session()->get('current_company_id');
        }

        if ($user->hasRole('company_admin')) {
            return $user->company_id;
        }

        if ($user->employee && $user->employee->company_id) {
            return $user->employee->company_id;
        }

        return null;
    }

    private function rowHasCompanyId($row): bool
    {
        if (is_array($row)) {
            return array_key_exists('company_id', $row);
        }

        if (method_exists($row, 'getAttributes')) {
            return array_key_exists('company_id', $row->getAttributes());
        }

        return false;
    }

    private function rowHasEmployeeId($row): bool
    {
        if (is_array($row)) {
            return array_key_exists('employee_id', $row);
        }

        if (method_exists($row, 'getAttributes')) {
            return array_key_exists('employee_id', $row->getAttributes());
        }

        return false;
    }

    private function isInUserScope(User $user, $row): bool
    {
        // Allow user to access their own user record
        if (method_exists($row, 'getTable') && $row->getTable() === 'users' && $user->id === $row->id) {
            return true;
        }

        $userCompanyId = $this->getUserCompanyId($user);

        if ($userCompanyId && $this->rowHasCompanyId($row)) {
            $rowCompanyId = $row->company_id;
            if ($user->hasRole('super_admin') && !session()->has('current_company_id')) {
                return true;
            }
            if ($rowCompanyId !== null && (int) $rowCompanyId !== (int) $userCompanyId) {
                return false;
            }
        }

        if ($this->rowHasEmployeeId($row)) {
            $employeeId = $row->employee_id ?? $row->id;
            if ($user->hasRole('employee')) {
                return $user->employee_id == $employeeId;
            }
            if ($user->hasRole('manager')) {
                $managedEmployeeIds = $user->managedEmployees()->pluck('id')->toArray();
                return in_array($employeeId, $managedEmployeeIds);
            }
        }

        if ($user->hasAnyRole(self::ADMIN_ROLES)) {
            return true;
        }

        return false;
    }
}
