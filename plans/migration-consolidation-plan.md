# Migration Consolidation Plan

> **Generated:** 2026-07-30  
> **Project:** Quick-HR  
> **Status:** READ-ONLY analysis — no files modified  
> **Total migration files scanned:** 70

---

## 1. Complete Migration Inventory

### 1.1 `database/migrations/` (21 files)

| # | Filename | Table(s) | Type | Summary |
|---|----------|----------|------|---------|
| 1 | [`2014_10_12_000000_create_users_table.php`](database/migrations/2014_10_12_000000_create_users_table.php) | `users` | CREATE | id, name, email (unique), status (default "invited"), password, email_verified_at (nullable), rememberToken, timestamps, softDeletes, indexes on status/deleted_at/(status+email) |
| 2 | [`2014_10_12_100000_create_password_reset_tokens_table.php`](database/migrations/2014_10_12_100000_create_password_reset_tokens_table.php) | `password_reset_tokens` | CREATE | email (PK), token, created_at |
| 3 | [`2014_10_12_200000_add_two_factor_columns_to_users_table.php`](database/migrations/2014_10_12_200000_add_two_factor_columns_to_users_table.php) | `users` | **ALTER** | Adds `two_factor_secret` (text, nullable), `two_factor_recovery_codes` (text, nullable), `two_factor_confirmed_at` (timestamp, nullable) |
| 4 | [`2019_08_19_000000_create_failed_jobs_table.php`](database/migrations/2019_08_19_000000_create_failed_jobs_table.php) | `failed_jobs` | CREATE | id, uuid (unique), connection, queue, payload, exception, failed_at |
| 5 | [`2019_12_14_000001_create_personal_access_tokens_table.php`](database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php) | `personal_access_tokens` | CREATE | id, tokenable (morphs), name, token (unique 64), abilities (text), last_used_at, **expires_at** (already present!), timestamps |
| 6 | [`2026_1_create_exports_table.php`](database/migrations/2026_1_create_exports_table.php) | `exports` | CREATE | id, user_id (FK→users), config_key, filters (json), columns (json), format, options (json), status, total_rows, chunk_size, total_chunks, file_path, file_size, download_token (unique), expires_at, error_message, completed_at, timestamps |
| 7 | [`2026_1_create_imports_table.php`](database/migrations/2026_1_create_imports_table.php) | `imports` | CREATE | id, config_key, file_path, original_filename, processed_rows, successful_rows, failed_rows, errors (json), error_file, status, total_rows, chunk_size, total_chunks, user_id (FK→users), timestamps |
| 8 | [`2026_2_create_ export_chunks_table.php`](database/migrations/2026_2_create_%20export_chunks_table.php) | `export_chunks` | CREATE | id, export_id (FK→exports), chunk_index, file_path, timestamps, unique(export_id, chunk_index) |
| 9 | [`2026_2_create_import_chunks_table.php`](database/migrations/2026_2_create_import_chunks_table.php) | `import_chunks` | CREATE | id, import_id (FK→imports), chunk_index, offset, limit, status, processed_rows, successful_rows, failed_rows, errors (json), timestamps, unique(import_id, chunk_index) |
| 10 | [`2026_07_17_000000_create_systems_table.php`](database/migrations/2026_07_17_000000_create_systems_table.php) | `systems` | CREATE | id, timestamps. Also seeds singleton row `{id: 1}` via `System::create()` |
| 11 | [`2026_07_21_095547_create_jobs_table.php`](database/migrations/2026_07_21_095547_create_jobs_table.php) | `jobs` | **DEAD** | `up()` fully commented out. Duplicate of #14. |
| 12 | [`2026_07_25_214000_add_finalized_at_to_payroll_runs.php`](database/migrations/2026_07_25_214000_add_finalized_at_to_payroll_runs.php) | `payroll_runs` | **ALTER** | Adds `finalized_at` (timestamp, nullable) after `calculation_status` |
| 13 | [`2026_07_28_000000_add_not_null_constraints_to_attendances.php`](database/migrations/2026_07_28_000000_add_not_null_constraints_to_attendances.php) | `attendances` | **ALTER** | Changes `regular_hours`, `overtime_hours`, `double_time_hours`, `net_hours` from `->nullable()` to `->nullable(false)->default(0)` |
| 14 | [`add_create_jobs_table.php`](database/migrations/add_create_jobs_table.php) | `jobs` | CREATE | id, queue (index), payload, attempts, reserved_at, available_at, created_at |
| 15 | [`add_expires_at_to_personal_access_tokens_table.php`](database/migrations/add_expires_at_to_personal_access_tokens_table.php) | `personal_access_tokens` | **DEAD** | `up()` fully commented out. Column `expires_at` already exists in CREATE (#5). |
| 16 | [`add_has_seen_tour_to_users_table.php`](database/migrations/add_has_seen_tour_to_users_table.php) | `users` | **ALTER** | Adds `has_seen_tour` (boolean, default false) |
| 17 | [`add_missing_columns_to_users_table.php`](database/migrations/add_missing_columns_to_users_table.php) | `users` | **ALTER** | Adds `email_verified_at` (if missing — already in CREATE), `user_type` (default 'user'), `status` (default 'active' — already in CREATE with 'invited'), `company_id` (nullable integer) |
| 18 | [`create_payroll_run_progress.php`](database/migrations/create_payroll_run_progress.php) | `payroll_run_progress` | CREATE | id, payroll_run_id (FK→payroll_runs), total_employees, processed_employees, status, timestamps |
| 19 | [`create_saved_filters_table.php`](database/migrations/create_saved_filters_table.php) | `saved_filters` | CREATE | id, user_id (FK→users), config_key, name, filters (json), is_global, timestamps |
| 20 | [`create_saved_reports_table.php`](database/migrations/create_saved_reports_table.php) | `saved_reports` | CREATE | id, user_id (FK→users), config_key, name, type, configuration (json), is_global, timestamps |
| 21 | [`create_system_settings_table.php`](database/migrations/create_system_settings_table.php) | `system_settings` | CREATE | id, settingable (morphs), key, value (json), group, is_public, timestamps, unique(settingable_type, settingable_id, key) |

### 1.2 `app/Modules/Hr/Database/Migrations/` (46 files)

| # | Filename | Table(s) | Type | Summary |
|---|----------|----------|------|---------|
| 22 | [`2026_06_12_142453_create_companies_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142453_create_companies_table.php) | `companies` | CREATE | Company master table |
| 23 | [`2026_06_12_142455_create_locations_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142455_create_locations_table.php) | `locations` | CREATE | Physical locations |
| 24 | [`2026_06_12_142456_create_departments_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142456_create_departments_table.php) | `departments` | CREATE | Departments |
| 25 | [`2026_06_12_142457_create_job_titles_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142457_create_job_titles_table.php) | `job_titles` | CREATE | Job titles |
| 26 | [`2026_06_12_142458_create_attendance_policies_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142458_create_attendance_policies_table.php) | `attendance_policies` | CREATE | **Has ALTER** — see Pair 5 |
| 27 | [`2026_06_12_142458_create_shifts_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142458_create_shifts_table.php) | `shifts` | CREATE | Work shifts |
| 28 | [`2026_06_12_142459_create_employee_groups_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142459_create_employee_groups_table.php) | `employee_groups` | CREATE | Employee groups |
| 29 | [`2026_06_12_142500_create_tags_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142500_create_tags_table.php) | `tags` | CREATE | Tags |
| 30 | [`2026_06_12_142501_create_employees_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142501_create_employees_table.php) | `employees` | CREATE | Employees master |
| 31 | [`2026_06_12_142502_create_taggable_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142502_create_taggable_table.php) | `taggable` | CREATE | Polymorphic tag pivot |
| 32 | [`2026_06_12_142503_create_employee_job_histories_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142503_create_employee_job_histories_table.php) | `employee_job_histories` | CREATE | Job history |
| 33 | [`2026_06_12_142504_create_employee_profiles_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142504_create_employee_profiles_table.php) | `employee_profiles` | CREATE | Employee profiles |
| 34 | [`2026_06_12_142505_create_teams_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142505_create_teams_table.php) | `teams` | CREATE | Teams |
| 35 | [`2026_06_12_142506_create_employee_team_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142506_create_employee_team_table.php) | `employee_team` | CREATE | Employee-team pivot |
| 36 | [`2026_06_12_142507_create_documents_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142507_create_documents_table.php) | `documents` | CREATE | Documents |
| 37 | [`2026_06_12_142508_create_policy_assignments_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142508_create_policy_assignments_table.php) | `policy_assignments` | CREATE | Policy assignments |
| 38 | [`2026_06_12_142509_create_work_patterns_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142509_create_work_patterns_table.php) | `work_patterns` | CREATE | Work patterns |
| 39 | [`2026_06_12_142510_create_employee_work_patterns_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142510_create_employee_work_patterns_table.php) | `employee_work_patterns` | CREATE | Employee-work pattern pivot |
| 40 | [`2026_06_12_142511_create_pay_schedules_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142511_create_pay_schedules_table.php) | `pay_schedules` | CREATE | Pay schedules |
| 41 | [`2026_06_12_142512_create_employee_payroll_profiles_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142512_create_employee_payroll_profiles_table.php) | `employee_payroll_profiles` | CREATE | Employee payroll profiles |
| 42 | [`2026_06_12_142513_create_payroll_runs_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142513_create_payroll_runs_table.php) | `payroll_runs` | CREATE | **Has ALTER** — see Pair 2 |
| 43 | [`2026_06_12_142514_create_payroll_payslips_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142514_create_payroll_payslips_table.php) | `payroll_payslips` | CREATE | Payroll payslips |
| 44 | [`2026_06_12_142515_create_payroll_policies_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142515_create_payroll_policies_table.php) | `payroll_policies` | CREATE | Payroll policies |
| 45 | [`2026_06_12_142516_create_payroll_run_adjustments_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142516_create_payroll_run_adjustments_table.php) | `payroll_run_adjustments` | CREATE | Payroll run adjustments |
| 46 | [`2026_06_12_142517_create_employee_adjustment_profiles_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142517_create_employee_adjustment_profiles_table.php) | `employee_adjustment_profiles` | CREATE | Employee adjustment profiles |
| 47 | [`2026_06_12_142518_create_payslip_items_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142518_create_payslip_items_table.php) | `payslip_items` | CREATE | Payslip items |
| 48 | [`2026_06_12_142519_create_payroll_policy_assignments_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142519_create_payroll_policy_assignments_table.php) | `payroll_policy_assignments` | CREATE | Payroll policy assignments |
| 49 | [`2026_06_12_142520_create_employee_positions_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142520_create_employee_positions_table.php) | `employee_positions` | CREATE | Employee positions |
| 50 | [`2026_06_12_142521_create_leave_types_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142521_create_leave_types_table.php) | `leave_types` | CREATE | Leave types |
| 51 | [`2026_06_12_142522_create_leave_requests_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142522_create_leave_requests_table.php) | `leave_requests` | CREATE | Leave requests |
| 52 | [`2026_06_12_142523_create_leave_balances_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142523_create_leave_balances_table.php) | `leave_balances` | CREATE | Leave balances |
| 53 | [`2026_06_12_142524_create_leave_approvers_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142524_create_leave_approvers_table.php) | `leave_approvers` | CREATE | Leave approvers |
| 54 | [`2026_06_12_142525_create_leave_approver_leave_type_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142525_create_leave_approver_leave_type_table.php) | `leave_approver_leave_type` | CREATE | Leave approver-leave type pivot |
| 55 | [`2026_06_12_142526_create_attendances_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142526_create_attendances_table.php) | `attendances` | CREATE | **Has ALTER** — see Pair 3 |
| 56 | [`2026_06_12_142527_create_attendance_adjustments_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142527_create_attendance_adjustments_table.php) | `attendance_adjustments` | CREATE | Attendance adjustments |
| 57 | [`2026_06_12_142528_create_shift_schedules_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142528_create_shift_schedules_table.php) | `shift_schedules` | CREATE | Shift schedules |
| 58 | [`2026_06_12_142529_create_clock_events_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142529_create_clock_events_table.php) | `clock_events` | CREATE | Clock events |
| 59 | [`2026_06_12_142530_create_attendance_sessions_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142530_create_attendance_sessions_table.php) | `attendance_sessions` | CREATE | **Has ALTER** — see Pair 4 |
| 60 | [`2026_06_12_142531_create_holiday_calendars_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142531_create_holiday_calendars_table.php) | `holiday_calendars` | CREATE | Holiday calendars |
| 61 | [`2026_06_12_142532_create_department_holiday_calendar_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142532_create_department_holiday_calendar_table.php) | `department_holiday_calendar` | CREATE | Department-holiday calendar pivot |
| 62 | [`2026_06_12_142533_create_holiday_calendar_location_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142533_create_holiday_calendar_location_table.php) | `holiday_calendar_location` | CREATE | Holiday calendar-location pivot |
| 63 | [`2026_06_12_142534_create_holidays_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142534_create_holidays_table.php) | `holidays` | CREATE | Holidays |
| 64 | [`2026_07_27_000000_make_attendance_sessions_start_time_nullable.php`](app/Modules/Hr/Database/Migrations/2026_07_27_000000_make_attendance_sessions_start_time_nullable.php) | `attendance_sessions` | **ALTER** | Changes `start_time` from `->nullable(false)` to `->nullable()` |
| 65 | [`2026_07_27_000001_change_break_rule_columns_to_string.php`](app/Modules/Hr/Database/Migrations/2026_07_27_000001_change_break_rule_columns_to_string.php) | `attendance_policies` | **ALTER** | Changes `requires_break_after_hours` (decimal→string) and `break_duration_minutes` (integer→string) |

### 1.3 `app/Modules/Admin/Database/Migrations/` (2 files)

| # | Filename | Table(s) | Type | Summary |
|---|----------|----------|------|---------|
| 66 | [`2025_01_01_000002_create_permission_tables.php`](app/Modules/Admin/Database/Migrations/2025_01_01_000002_create_permission_tables.php) | `permissions`, `roles`, `model_has_permissions`, `model_has_roles`, `role_has_permissions` | CREATE | Spatie permission package tables |
| 67 | [`2026_06_12_142452_create_activity_logs_table.php`](app/Modules/Admin/Database/Migrations/2026_06_12_142452_create_activity_logs_table.php) | `activity_logs` | CREATE | id, company_id, log_name, action, description, created_at, subject_type, subject_id, causer_type, causer_id, old_values (json), new_values (json), properties (json), updated_at, indexes |

### 1.4 `app/Modules/System/Database/Migrations/` (1 file)

| # | Filename | Table(s) | Type | Summary |
|---|----------|----------|------|---------|
| 68 | [`2026_06_12_142535_create_approval_tables.php`](app/Modules/System/Database/Migrations/2026_06_12_142535_create_approval_tables.php) | `approval_requests`, `approval_tiers`, `approval_tier_approvals`, `approval_logs` | CREATE + self-contained ALTER | Creates 4 tables, then adds FK on `approval_requests.current_tier_id` → `approval_tiers` within same migration |

---

## 2. Consolidation Pairs

### Pair 1: `users` table

| Role | File | What it does |
|------|------|--------------|
| **CREATE** | [`2014_10_12_000000_create_users_table.php`](database/migrations/2014_10_12_000000_create_users_table.php) | Base table: id, name, email, status, password, email_verified_at, rememberToken, timestamps, softDeletes |
| **ALTER → MERGE** | [`2014_10_12_200000_add_two_factor_columns_to_users_table.php`](database/migrations/2014_10_12_200000_add_two_factor_columns_to_users_table.php) | `two_factor_secret` (text, nullable), `two_factor_recovery_codes` (text, nullable), `two_factor_confirmed_at` (timestamp, nullable) |
| **ALTER → MERGE** | [`add_has_seen_tour_to_users_table.php`](database/migrations/add_has_seen_tour_to_users_table.php) | `has_seen_tour` (boolean, default false) |
| **ALTER → MERGE** | [`add_missing_columns_to_users_table.php`](database/migrations/add_missing_columns_to_users_table.php) | `user_type` (string, default 'user'), `company_id` (integer, nullable). **NOTE:** `email_verified_at` and `status` already exist in CREATE — skip these. |

**Columns to add to the CREATE migration:**

```php
// From add_two_factor_columns
$table->text('two_factor_secret')->after('password')->nullable();
$table->text('two_factor_recovery_codes')->after('two_factor_secret')->nullable();
$table->timestamp('two_factor_confirmed_at')->after('two_factor_recovery_codes')->nullable();

// From add_has_seen_tour
$table->boolean('has_seen_tour')->default(false);

// From add_missing_columns (only the ones NOT already in CREATE)
$table->string('user_type')->default('user')->after('password');
$table->integer('company_id')->nullable()->after('id');
```

**⚠️ CONFLICT NOTE:** The base CREATE already defines `status` as `default("invited")` and `email_verified_at` as `nullable()`. The `add_missing_columns_to_users_table` migration tries to add `status` with `default('active')` and `email_verified_at` conditionally. Since the CREATE already has these columns, the ALTER's `status` default change (`invited` → `active`) represents a **semantic conflict**. The consolidated migration should use `default('active')` (the ALTER's intent) since the ALTER was meant to run after the CREATE.

---

### Pair 2: `payroll_runs` table

| Role | File | What it does |
|------|------|--------------|
| **CREATE** | [`2026_06_12_142513_create_payroll_runs_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142513_create_payroll_runs_table.php) | Full payroll_runs schema (see §3.2) |
| **ALTER → MERGE** | [`2026_07_25_214000_add_finalized_at_to_payroll_runs.php`](database/migrations/2026_07_25_214000_add_finalized_at_to_payroll_runs.php) | `finalized_at` (timestamp, nullable) after `calculation_status` |

**Column to add to the CREATE migration:**

```php
$table->timestamp('finalized_at')->nullable()->after('calculation_status');
```

**🔴 CROSS-MODULE:** The ALTER is in `database/migrations/` but the CREATE is in `app/Modules/Hr/Database/Migrations/`. After consolidation, the `finalized_at` column will live in the Hr module's CREATE migration.

---

### Pair 3: `attendances` table

| Role | File | What it does |
|------|------|--------------|
| **CREATE** | [`2026_06_12_142526_create_attendances_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142526_create_attendances_table.php) | Full attendances schema |
| **ALTER → MERGE** | [`2026_07_28_000000_add_not_null_constraints_to_attendances.php`](database/migrations/2026_07_28_000000_add_not_null_constraints_to_attendances.php) | Changes `regular_hours`, `overtime_hours`, `double_time_hours`, `net_hours` from `->nullable()` to `->nullable(false)->default(0)` |

**Changes to apply in the CREATE migration:**

Replace these lines in the CREATE:
```php
$table->decimal('regular_hours', 6, 2)->nullable();
$table->decimal('overtime_hours', 6, 2)->nullable();
$table->decimal('double_time_hours', 6, 2)->nullable();
$table->decimal('net_hours', 6, 2)->nullable();
```

With:
```php
$table->decimal('regular_hours', 10, 2)->default(0)->nullable(false);
$table->decimal('overtime_hours', 10, 2)->default(0)->nullable(false);
$table->decimal('double_time_hours', 10, 2)->default(0)->nullable(false);
$table->decimal('net_hours', 10, 2)->default(0)->nullable(false);
```

**⚠️ PRECISION CHANGE NOTE:** The ALTER also changes precision from `(6, 2)` to `(10, 2)`. This must be reflected in the consolidated CREATE.

**🔴 CROSS-MODULE:** The ALTER is in `database/migrations/` but the CREATE is in `app/Modules/Hr/Database/Migrations/`.

---

### Pair 4: `attendance_sessions` table

| Role | File | What it does |
|------|------|--------------|
| **CREATE** | [`2026_06_12_142530_create_attendance_sessions_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142530_create_attendance_sessions_table.php) | Full attendance_sessions schema |
| **ALTER → MERGE** | [`2026_07_27_000000_make_attendance_sessions_start_time_nullable.php`](app/Modules/Hr/Database/Migrations/2026_07_27_000000_make_attendance_sessions_start_time_nullable.php) | Changes `start_time` from required to nullable |

**Change to apply in the CREATE migration:**

Replace:
```php
$table->datetime('start_time');
```

With:
```php
$table->datetime('start_time')->nullable();
```

---

### Pair 5: `attendance_policies` table

| Role | File | What it does |
|------|------|--------------|
| **CREATE** | [`2026_06_12_142458_create_attendance_policies_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142458_create_attendance_policies_table.php) | Full attendance_policies schema |
| **ALTER → MERGE** | [`2026_07_27_000001_change_break_rule_columns_to_string.php`](app/Modules/Hr/Database/Migrations/2026_07_27_000001_change_break_rule_columns_to_string.php) | Changes `requires_break_after_hours` (decimal→string) and `break_duration_minutes` (integer→string) |

**Changes to apply in the CREATE migration:**

Replace:
```php
$table->decimal('requires_break_after_hours', 4, 2)->default(5)->nullable();
$table->integer('break_duration_minutes')->default(30)->nullable();
```

With:
```php
$table->string('requires_break_after_hours', 100)->nullable()->default('5');
$table->string('break_duration_minutes', 100)->nullable()->default('30');
```

---

## 3. Detailed CREATE Migration Schemas (for reference)

### 3.1 `users` — Current CREATE schema

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('status')->default("invited");
    $table->string('password');
    $table->timestamp('email_verified_at')->nullable();
    $table->rememberToken();
    $table->timestamps();
    $table->softDeletes();
    $table->index('status');
    $table->index('deleted_at');
    $table->index(['status', 'email']);
});
```

### 3.2 `payroll_runs` — Current CREATE schema

```php
Schema::create('payroll_runs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->nullable()->constrained('companies', 'id')->onDelete('cascade');
    $table->index('company_id');
    $table->boolean('is_multi_company')->default(false);
    $table->string('title');
    $table->foreignId('pay_schedule_id')->nullable()->constrained('pay_schedules', 'id')->onDelete('restrict');
    $table->date('period_start');
    $table->date('period_end');
    $table->string('status')->default('draft')->nullable();
    $table->integer('current_step')->default(1)->nullable();
    $table->string('calculation_status')->default('pending')->nullable();
    // >>> finalized_at GOES HERE <<<
    $table->decimal('total_gross_pay', 15, 2)->default(0)->nullable();
    $table->decimal('total_deductions', 15, 2)->default(0)->nullable();
    $table->decimal('total_taxes', 15, 2)->default(0)->nullable();
    $table->decimal('total_employer_contributions', 15, 2)->default(0)->nullable();
    $table->decimal('total_cash_required', 15, 2)->default(0)->nullable();
    $table->string('processed_by')->nullable();
    $table->datetime('processed_at')->nullable();
    $table->string('base_currency')->default('USD')->nullable();
    $table->date('payment_date')->nullable();
    $table->string('reconciliation_status')->default('pending')->nullable();
    $table->datetime('reconciled_at')->nullable();
    $table->string('payment_batch_id')->nullable();
    $table->decimal('total_employee_contributions', 15, 2)->default(0)->nullable();
    $table->decimal('total_income_tax', 15, 2)->default(0)->nullable();
    $table->decimal('total_bonus', 15, 2)->default(0)->nullable();
    $table->decimal('total_commission', 15, 2)->default(0)->nullable();
    $table->decimal('total_reimbursement', 15, 2)->default(0)->nullable();
    $table->foreignId('approved_by_user_id')->nullable()->constrained('users', 'id')->onDelete('set null');
    $table->datetime('approved_at')->nullable();
    $table->integer('total_employees')->default(0)->nullable();
    $table->integer('processed_employees')->default(0)->nullable();
    $table->datetime('failed_at')->nullable();
    $table->text('failure_reason')->nullable();
    $table->json('per_company_summaries')->nullable();
    $table->text('notes')->nullable();
    $table->integer('created_by')->nullable();
    $table->integer('updated_by')->nullable();
    // ... indexes ...
    $table->timestamps();
});
```

### 3.3 `attendances` — Current CREATE schema

Key columns affected by consolidation:
```php
// Current (lines 40-42):
$table->decimal('regular_hours', 6, 2)->nullable();
$table->decimal('overtime_hours', 6, 2)->nullable();
$table->decimal('double_time_hours', 6, 2)->nullable();
// Current (line 22):
$table->decimal('net_hours', 6, 2)->nullable();

// After consolidation:
$table->decimal('regular_hours', 10, 2)->default(0)->nullable(false);
$table->decimal('overtime_hours', 10, 2)->default(0)->nullable(false);
$table->decimal('double_time_hours', 10, 2)->default(0)->nullable(false);
$table->decimal('net_hours', 10, 2)->default(0)->nullable(false);
```

### 3.4 `attendance_sessions` — Current CREATE schema

```php
// Current (line 16):
$table->datetime('start_time');

// After consolidation:
$table->datetime('start_time')->nullable();
```

### 3.5 `attendance_policies` — Current CREATE schema

```php
// Current (lines 26-27):
$table->decimal('requires_break_after_hours', 4, 2)->default(5)->nullable();
$table->integer('break_duration_minutes')->default(30)->nullable();

// After consolidation:
$table->string('requires_break_after_hours', 100)->nullable()->default('5');
$table->string('break_duration_minutes', 100)->nullable()->default('30');
```

---

## 4. Files to DELETE After Consolidation

### 4.1 ALTER migrations merged into CREATE (DELETE these)

| # | File | Merged Into |
|---|------|-------------|
| 1 | [`database/migrations/2014_10_12_200000_add_two_factor_columns_to_users_table.php`](database/migrations/2014_10_12_200000_add_two_factor_columns_to_users_table.php) | `create_users_table` |
| 2 | [`database/migrations/add_has_seen_tour_to_users_table.php`](database/migrations/add_has_seen_tour_to_users_table.php) | `create_users_table` |
| 3 | [`database/migrations/add_missing_columns_to_users_table.php`](database/migrations/add_missing_columns_to_users_table.php) | `create_users_table` |
| 4 | [`database/migrations/2026_07_25_214000_add_finalized_at_to_payroll_runs.php`](database/migrations/2026_07_25_214000_add_finalized_at_to_payroll_runs.php) | `create_payroll_runs_table` (Hr module) |
| 5 | [`database/migrations/2026_07_28_000000_add_not_null_constraints_to_attendances.php`](database/migrations/2026_07_28_000000_add_not_null_constraints_to_attendances.php) | `create_attendances_table` (Hr module) |
| 6 | [`app/Modules/Hr/Database/Migrations/2026_07_27_000000_make_attendance_sessions_start_time_nullable.php`](app/Modules/Hr/Database/Migrations/2026_07_27_000000_make_attendance_sessions_start_time_nullable.php) | `create_attendance_sessions_table` (Hr module) |
| 7 | [`app/Modules/Hr/Database/Migrations/2026_07_27_000001_change_break_rule_columns_to_string.php`](app/Modules/Hr/Database/Migrations/2026_07_27_000001_change_break_rule_columns_to_string.php) | `create_attendance_policies_table` (Hr module) |

### 4.2 Dead migrations (fully commented out — DELETE these)

| # | File | Reason |
|---|------|--------|
| 8 | [`database/migrations/2026_07_21_095547_create_jobs_table.php`](database/migrations/2026_07_21_095547_create_jobs_table.php) | `up()` fully commented out. Duplicate of `add_create_jobs_table.php`. |
| 9 | [`database/migrations/add_expires_at_to_personal_access_tokens_table.php`](database/migrations/add_expires_at_to_personal_access_tokens_table.php) | `up()` fully commented out. `expires_at` already exists in `create_personal_access_tokens_table`. |

---

## 5. Special Cases & Cross-Module Dependencies

### 5.1 Cross-Module ALTER Migrations

Two ALTER migrations live in `database/migrations/` but modify tables created in `app/Modules/Hr/Database/Migrations/`:

| ALTER (in `database/migrations/`) | Target Table | CREATE (in Hr module) |
|-----------------------------------|-------------|----------------------|
| [`2026_07_25_214000_add_finalized_at_to_payroll_runs.php`](database/migrations/2026_07_25_214000_add_finalized_at_to_payroll_runs.php) | `payroll_runs` | [`2026_06_12_142513_create_payroll_runs_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142513_create_payroll_runs_table.php) |
| [`2026_07_28_000000_add_not_null_constraints_to_attendances.php`](database/migrations/2026_07_28_000000_add_not_null_constraints_to_attendances.php) | `attendances` | [`2026_06_12_142526_create_attendances_table.php`](app/Modules/Hr/Database/Migrations/2026_06_12_142526_create_attendances_table.php) |

**Resolution:** Merge the ALTER content into the Hr module CREATE migrations. The ALTER files in `database/migrations/` are deleted.

### 5.2 `users` Table — Conflicting Defaults

The base [`create_users_table`](database/migrations/2014_10_12_000000_create_users_table.php) defines:
```php
$table->string('status')->default("invited");
$table->timestamp('email_verified_at')->nullable();
```

The [`add_missing_columns_to_users_table`](database/migrations/add_missing_columns_to_users_table.php) tries to add:
```php
$table->string('status')->default('active')->after('user_type');  // CONFLICT: different default
$table->timestamp('email_verified_at')->nullable()->after('email'); // DUPLICATE: already exists
```

**Resolution:** 
- `email_verified_at` — already exists in CREATE; skip the ALTER's attempt to add it.
- `status` — already exists in CREATE with `default("invited")`. The ALTER's `default('active')` was intended to override this. **Use `default('active')`** in the consolidated migration since the ALTER represents the final desired state.
- `user_type` and `company_id` — genuinely new columns; add them.

### 5.3 `attendances` Table — Precision Change

The ALTER [`2026_07_28_000000_add_not_null_constraints_to_attendances.php`](database/migrations/2026_07_28_000000_add_not_null_constraints_to_attendances.php) changes column precision from `(6, 2)` to `(10, 2)` in addition to making them non-nullable. The consolidated CREATE must use `(10, 2)`.

### 5.4 `approval_tables` — Self-Contained ALTER Within CREATE

[`2026_06_12_142535_create_approval_tables.php`](app/Modules/System/Database/Migrations/2026_06_12_142535_create_approval_tables.php) creates 4 tables then adds a circular FK (`approval_requests.current_tier_id` → `approval_tiers`) within the same migration. This is already consolidated — **no action needed**.

### 5.5 `create_payroll_run_progress` — References Hr Module Table

[`create_payroll_run_progress.php`](database/migrations/create_payroll_run_progress.php) creates `payroll_run_progress` with a FK to `payroll_runs` (created in Hr module). This is a legitimate cross-module dependency that must run AFTER the Hr module's `create_payroll_runs_table`. **Do NOT consolidate** — keep as standalone.

### 5.6 `create_system_settings_table` — Polymorphic

[`create_system_settings_table.php`](database/migrations/create_system_settings_table.php) uses `$table->morphs('settingable')` — it can reference any model. No specific table dependency. **Keep standalone.**

### 5.7 `create_systems_table` — Seeds Data

[`2026_07_17_000000_create_systems_table.php`](database/migrations/2026_07_17_000000_create_systems_table.php) seeds a singleton row in `up()`. This is unusual for a migration but is self-contained. **Keep standalone.**

---

## 6. Recommended Execution Order (Post-Consolidation)

After consolidation, migrations should run in this order. The numbering reflects the dependency chain:

### Phase 1: Foundation (database/migrations/)

| Order | Migration | Creates |
|-------|-----------|---------|
| 1 | `2014_10_12_000000_create_users_table` | `users` (consolidated — includes 2FA, tour, user_type, company_id) |
| 2 | `2014_10_12_100000_create_password_reset_tokens_table` | `password_reset_tokens` |
| 3 | `2019_08_19_000000_create_failed_jobs_table` | `failed_jobs` |
| 4 | `2019_12_14_000001_create_personal_access_tokens_table` | `personal_access_tokens` |
| 5 | `add_create_jobs_table` | `jobs` |
| 6 | `2026_07_17_000000_create_systems_table` | `systems` |

### Phase 2: Admin Module

| Order | Migration | Creates |
|-------|-----------|---------|
| 7 | `2025_01_01_000002_create_permission_tables` | `permissions`, `roles`, `model_has_permissions`, `model_has_roles`, `role_has_permissions` |
| 8 | `2026_06_12_142452_create_activity_logs_table` | `activity_logs` |

### Phase 3: Hr Module (respects internal dependency order)

| Order | Migration | Creates |
|-------|-----------|---------|
| 9 | `2026_06_12_142453_create_companies_table` | `companies` |
| 10 | `2026_06_12_142455_create_locations_table` | `locations` |
| 11 | `2026_06_12_142456_create_departments_table` | `departments` |
| 12 | `2026_06_12_142457_create_job_titles_table` | `job_titles` |
| 13 | `2026_06_12_142458_create_attendance_policies_table` | `attendance_policies` **(consolidated — string break rules)** |
| 14 | `2026_06_12_142458_create_shifts_table` | `shifts` |
| 15 | `2026_06_12_142459_create_employee_groups_table` | `employee_groups` |
| 16 | `2026_06_12_142500_create_tags_table` | `tags` |
| 17 | `2026_06_12_142501_create_employees_table` | `employees` |
| 18 | `2026_06_12_142502_create_taggable_table` | `taggable` |
| 19 | `2026_06_12_142503_create_employee_job_histories_table` | `employee_job_histories` |
| 20 | `2026_06_12_142504_create_employee_profiles_table` | `employee_profiles` |
| 21 | `2026_06_12_142505_create_teams_table` | `teams` |
| 22 | `2026_06_12_142506_create_employee_team_table` | `employee_team` |
| 23 | `2026_06_12_142507_create_documents_table` | `documents` |
| 24 | `2026_06_12_142508_create_policy_assignments_table` | `policy_assignments` |
| 25 | `2026_06_12_142509_create_work_patterns_table` | `work_patterns` |
| 26 | `2026_06_12_142510_create_employee_work_patterns_table` | `employee_work_patterns` |
| 27 | `2026_06_12_142511_create_pay_schedules_table` | `pay_schedules` |
| 28 | `2026_06_12_142512_create_employee_payroll_profiles_table` | `employee_payroll_profiles` |
| 29 | `2026_06_12_142513_create_payroll_runs_table` | `payroll_runs` **(consolidated — includes finalized_at)** |
| 30 | `2026_06_12_142514_create_payroll_payslips_table` | `payroll_payslips` |
| 31 | `2026_06_12_142515_create_payroll_policies_table` | `payroll_policies` |
| 32 | `2026_06_12_142516_create_payroll_run_adjustments_table` | `payroll_run_adjustments` |
| 33 | `2026_06_12_142517_create_employee_adjustment_profiles_table` | `employee_adjustment_profiles` |
| 34 | `2026_06_12_142518_create_payslip_items_table` | `payslip_items` |
| 35 | `2026_06_12_142519_create_payroll_policy_assignments_table` | `payroll_policy_assignments` |
| 36 | `2026_06_12_142520_create_employee_positions_table` | `employee_positions` |
| 37 | `2026_06_12_142521_create_leave_types_table` | `leave_types` |
| 38 | `2026_06_12_142522_create_leave_requests_table` | `leave_requests` |
| 39 | `2026_06_12_142523_create_leave_balances_table` | `leave_balances` |
| 40 | `2026_06_12_142524_create_leave_approvers_table` | `leave_approvers` |
| 41 | `2026_06_12_142525_create_leave_approver_leave_type_table` | `leave_approver_leave_type` |
| 42 | `2026_06_12_142526_create_attendances_table` | `attendances` **(consolidated — non-null hours, precision 10,2)** |
| 43 | `2026_06_12_142527_create_attendance_adjustments_table` | `attendance_adjustments` |
| 44 | `2026_06_12_142528_create_shift_schedules_table` | `shift_schedules` |
| 45 | `2026_06_12_142529_create_clock_events_table` | `clock_events` |
| 46 | `2026_06_12_142530_create_attendance_sessions_table` | `attendance_sessions` **(consolidated — start_time nullable)** |
| 47 | `2026_06_12_142531_create_holiday_calendars_table` | `holiday_calendars` |
| 48 | `2026_06_12_142532_create_department_holiday_calendar_table` | `department_holiday_calendar` |
| 49 | `2026_06_12_142533_create_holiday_calendar_location_table` | `holiday_calendar_location` |
| 50 | `2026_06_12_142534_create_holidays_table` | `holidays` |

### Phase 4: System Module

| Order | Migration | Creates |
|-------|-----------|---------|
| 51 | `2026_06_12_142535_create_approval_tables` | `approval_requests`, `approval_tiers`, `approval_tier_approvals`, `approval_logs` |

### Phase 5: Cross-Module Dependents (database/migrations/)

| Order | Migration | Creates | Depends On |
|-------|-----------|---------|------------|
| 52 | `create_payroll_run_progress` | `payroll_run_progress` | `payroll_runs` (Hr module) |
| 53 | `2026_1_create_exports_table` | `exports` | `users` |
| 54 | `2026_1_create_imports_table` | `imports` | `users` |
| 55 | `2026_2_create_export_chunks_table` | `export_chunks` | `exports` |
| 56 | `2026_2_create_import_chunks_table` | `import_chunks` | `imports` |
| 57 | `create_saved_filters_table` | `saved_filters` | `users` |
| 58 | `create_saved_reports_table` | `saved_reports` | `users` |
| 59 | `create_system_settings_table` | `system_settings` | (polymorphic — no specific dependency) |

---

## 7. Summary Statistics

| Metric | Count |
|--------|-------|
| Total migration files scanned | 70 |
| CREATE migrations | 61 |
| ALTER migrations | 7 |
| Dead migrations (commented out) | 2 |
| Consolidation pairs identified | 5 |
| Files to DELETE (ALTERs merged) | 7 |
| Files to DELETE (dead) | 2 |
| **Total files to DELETE** | **9** |
| **Files after consolidation** | **61** |
| Cross-module ALTERs resolved | 2 |
| Standalone migrations (no changes) | 54 |

---

## 8. Risk Assessment

| Risk | Level | Mitigation |
|------|-------|------------|
| `users.status` default conflict (`invited` vs `active`) | **MEDIUM** | Use `active` (ALTER's final intent). Verify no code relies on `invited` as default. |
| `attendances` precision change (6,2 → 10,2) | **LOW** | Higher precision is backward-compatible. |
| `attendance_policies` column type change (decimal→string) | **LOW** | String can hold decimal values; code already expects JSON arrays. |
| `attendance_sessions.start_time` nullable change | **LOW** | Relaxing constraint is backward-compatible. |
| Cross-module FK in `create_payroll_run_progress` | **LOW** | Kept as standalone; runs after Hr module. |
| `create_systems_table` seeds data in migration | **LOW** | Self-contained; no changes needed. |
