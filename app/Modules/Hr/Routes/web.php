<?php

use Illuminate\Support\Facades\Route;



use App\Modules\Hr\Http\Controllers\PayrollRunController;
use App\Modules\Hr\Http\Controllers\PayrollReportController;
use App\Modules\Hr\Http\Controllers\PayslipController;

use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use App\Modules\Hr\Http\Livewire\AdjustAttendanceMvp;
use App\Modules\Hr\Http\Controllers\EmployeePrintController;



Route::middleware([
    'web',
    // InitializeTenancyByDomain::class,
    // PreventAccessFromCentralDomains::class,

])->group(function () {





    // In your web.php or hr module routes

    /*Route::get('/hr/attendance/{attendanceId}/adjust', function ($attendanceId) {
        return view('hr::adjust-attendance', ['attendanceId' => $attendanceId]);
    } )->name('attendance.adjust');*/





    // Preview modal
    Route::get('/hr/payroll-runs/{payrollRun}/preview', [PayrollRunController::class, 'preview'])
        ->name('payroll.runs.preview');

    // Approve action
    Route::post('/hr/payroll-runs/{payrollRun}/approve', [PayrollRunController::class, 'approve'])
        ->name('payroll.runs.approve');


    // Preview modal
    Route::get('/hr/payroll-runs/{payrollRun}/edit', [PayrollRunController::class, 'edit'])
        ->name('payroll.payroll-employees.edit');


    // Payroll Reports
    Route::get('/hr/payroll-runs/{payrollRun}/report', [PayrollReportController::class, 'show'])
        ->name('payroll.reports.show');

    Route::get('/hr/payroll-runs/{payrollRun}/report/download/pdf', [PayrollReportController::class, 'downloadPdf'])
        ->name('payroll.reports.download.pdf');

    Route::get('/hr/payroll-runs/{payrollRun}/report/download/excel', [PayrollReportController::class, 'downloadExcel'])
        ->name('payroll.reports.download.excel');



    // Employee payslips
    Route::get('/hr/payslips/{payslip}', [PayslipController::class, 'download'])
        ->name('payslips.download');
    //->middleware('auth');

    // HR admin payslips
    Route::get('/hr/payslips/{payslip}/view', [PayslipController::class, 'view'])
        ->name('payslips.view');
    //->middleware('can:manage-payroll');



    // Route::post('/payroll-runs/{payrollRun}/generate-payslips', [PayrollRunController::class, 'generatePayslips']);
    // Route::post('/payroll-runs/{payrollRun}/mark-as-paid', [PayrollRunController::class, 'markAsPaid']);





    Route::get('/employees/{employee}/print', [EmployeePrintController::class, 'show'])
        ->name('hr.employees.print')
        //->middleware(['auth', 'can:view,employee']);
        ->middleware(['auth']);



});


// Routes for Employee

// Create Route
Route::get('employees/create', function (\Illuminate\Http\Request $request) {
    return view('hr::employees.create', [
        'configKey' => 'hr.employee',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('employees.create');

// Show Route
Route::get('employees/{id}', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::employees.show', [
        'recordId' => (int) $id,
        'configKey' => 'hr.employee',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('employees.show')->where('id', '[0-9]+'); 

// Edit Route
Route::get('employees/{id}/edit', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::employees.edit', [
        'recordId' => (int) $id,
        'configKey' => 'hr.employee',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('employees.edit')->where('id', '[0-9]+'); // And here;


// Routes for EmployeeJobHistory

// Create Route
Route::get('employee-job-histories/create', function (\Illuminate\Http\Request $request) {
    return view('hr::employee-job-histories.create', [
        'configKey' => 'hr.employee_job_history',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('employee-job-histories.create');

// Show Route
Route::get('employee-job-histories/{id}', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::employee-job-histories.show', [
        'recordId' => (int) $id,
        'configKey' => 'hr.employee_job_history',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('employee-job-histories.show')->where('id', '[0-9]+'); 

// Edit Route
Route::get('employee-job-histories/{id}/edit', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::employee-job-histories.edit', [
        'recordId' => (int) $id,
        'configKey' => 'hr.employee_job_history',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('employee-job-histories.edit')->where('id', '[0-9]+'); // And here;


// Routes for EmployeeProfile

// Create Route
Route::get('employee-profiles/create', function (\Illuminate\Http\Request $request) {
    return view('hr::employee-profiles.create', [
        'configKey' => 'hr.employee_profile',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('employee-profiles.create');

// Show Route
Route::get('employee-profiles/{id}', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::employee-profiles.show', [
        'recordId' => (int) $id,
        'configKey' => 'hr.employee_profile',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('employee-profiles.show')->where('id', '[0-9]+'); 

// Edit Route
Route::get('employee-profiles/{id}/edit', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::employee-profiles.edit', [
        'recordId' => (int) $id,
        'configKey' => 'hr.employee_profile',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('employee-profiles.edit')->where('id', '[0-9]+'); // And here;


// Routes for Document

// Create Route
Route::get('documents/create', function (\Illuminate\Http\Request $request) {
    return view('hr::documents.create', [
        'configKey' => 'hr.document',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('documents.create');

// Show Route
Route::get('documents/{id}', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::documents.show', [
        'recordId' => (int) $id,
        'configKey' => 'hr.document',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('documents.show')->where('id', '[0-9]+'); 

// Edit Route
Route::get('documents/{id}/edit', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::documents.edit', [
        'recordId' => (int) $id,
        'configKey' => 'hr.document',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('documents.edit')->where('id', '[0-9]+'); // And here;


// Routes for PaySchedule

// Create Route
Route::get('pay-schedules/create', function (\Illuminate\Http\Request $request) {
    return view('hr::pay-schedules.create', [
        'configKey' => 'hr.pay_schedule',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('pay-schedules.create');

// Show Route
Route::get('pay-schedules/{id}', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::pay-schedules.show', [
        'recordId' => (int) $id,
        'configKey' => 'hr.pay_schedule',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('pay-schedules.show')->where('id', '[0-9]+'); 

// Edit Route
Route::get('pay-schedules/{id}/edit', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::pay-schedules.edit', [
        'recordId' => (int) $id,
        'configKey' => 'hr.pay_schedule',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('pay-schedules.edit')->where('id', '[0-9]+'); // And here;


// Routes for EmployeePayrollProfile

// Create Route
Route::get('employee-payroll-profiles/create', function (\Illuminate\Http\Request $request) {
    return view('hr::employee-payroll-profiles.create', [
        'configKey' => 'hr.employee_payroll_profile',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('employee-payroll-profiles.create');

// Show Route
Route::get('employee-payroll-profiles/{id}', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::employee-payroll-profiles.show', [
        'recordId' => (int) $id,
        'configKey' => 'hr.employee_payroll_profile',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('employee-payroll-profiles.show')->where('id', '[0-9]+'); 

// Edit Route
Route::get('employee-payroll-profiles/{id}/edit', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::employee-payroll-profiles.edit', [
        'recordId' => (int) $id,
        'configKey' => 'hr.employee_payroll_profile',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('employee-payroll-profiles.edit')->where('id', '[0-9]+'); // And here;


// Routes for PayrollRun

// Create Route
Route::get('payroll-runs/create', function (\Illuminate\Http\Request $request) {
    return view('hr::payroll-runs.create', [
        'configKey' => 'hr.payroll_run',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('payroll-runs.create');

// Show Route
Route::get('payroll-runs/{id}', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::payroll-runs.show', [
        'recordId' => (int) $id,
        'configKey' => 'hr.payroll_run',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('payroll-runs.show')->where('id', '[0-9]+'); 

// Edit Route
Route::get('payroll-runs/{id}/edit', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::payroll-runs.edit', [
        'recordId' => (int) $id,
        'configKey' => 'hr.payroll_run',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('payroll-runs.edit')->where('id', '[0-9]+'); // And here;


// Routes for PayrollPayslip

// Create Route
Route::get('payroll-payslips/create', function (\Illuminate\Http\Request $request) {
    return view('hr::payroll-payslips.create', [
        'configKey' => 'hr.payroll_payslip',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('payroll-payslips.create');

// Show Route
Route::get('payroll-payslips/{id}', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::payroll-payslips.show', [
        'recordId' => (int) $id,
        'configKey' => 'hr.payroll_payslip',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('payroll-payslips.show')->where('id', '[0-9]+'); 

// Edit Route
Route::get('payroll-payslips/{id}/edit', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::payroll-payslips.edit', [
        'recordId' => (int) $id,
        'configKey' => 'hr.payroll_payslip',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('payroll-payslips.edit')->where('id', '[0-9]+'); // And here;


// Routes for PayrollPolicy

// Create Route
Route::get('payroll-policies/create', function (\Illuminate\Http\Request $request) {
    return view('hr::payroll-policies.create', [
        'configKey' => 'hr.payroll_policy',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('payroll-policies.create');

// Show Route
Route::get('payroll-policies/{id}', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::payroll-policies.show', [
        'recordId' => (int) $id,
        'configKey' => 'hr.payroll_policy',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('payroll-policies.show')->where('id', '[0-9]+'); 

// Edit Route
Route::get('payroll-policies/{id}/edit', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::payroll-policies.edit', [
        'recordId' => (int) $id,
        'configKey' => 'hr.payroll_policy',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('payroll-policies.edit')->where('id', '[0-9]+'); // And here;


// Routes for PayrollRunAdjustment

// Create Route
Route::get('payroll-run-adjustments/create', function (\Illuminate\Http\Request $request) {
    return view('hr::payroll-run-adjustments.create', [
        'configKey' => 'hr.payroll_run_adjustment',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('payroll-run-adjustments.create');

// Show Route
Route::get('payroll-run-adjustments/{id}', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::payroll-run-adjustments.show', [
        'recordId' => (int) $id,
        'configKey' => 'hr.payroll_run_adjustment',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('payroll-run-adjustments.show')->where('id', '[0-9]+'); 

// Edit Route
Route::get('payroll-run-adjustments/{id}/edit', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::payroll-run-adjustments.edit', [
        'recordId' => (int) $id,
        'configKey' => 'hr.payroll_run_adjustment',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('payroll-run-adjustments.edit')->where('id', '[0-9]+'); // And here;


// Routes for EmployeeAdjustmentProfile

// Create Route
Route::get('employee-adjustment-profiles/create', function (\Illuminate\Http\Request $request) {
    return view('hr::employee-adjustment-profiles.create', [
        'configKey' => 'hr.employee_adjustment_profile',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('employee-adjustment-profiles.create');

// Show Route
Route::get('employee-adjustment-profiles/{id}', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::employee-adjustment-profiles.show', [
        'recordId' => (int) $id,
        'configKey' => 'hr.employee_adjustment_profile',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('employee-adjustment-profiles.show')->where('id', '[0-9]+'); 

// Edit Route
Route::get('employee-adjustment-profiles/{id}/edit', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::employee-adjustment-profiles.edit', [
        'recordId' => (int) $id,
        'configKey' => 'hr.employee_adjustment_profile',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('employee-adjustment-profiles.edit')->where('id', '[0-9]+'); // And here;


// Routes for PayslipItem

// Create Route
Route::get('payslip-items/create', function (\Illuminate\Http\Request $request) {
    return view('hr::payslip-items.create', [
        'configKey' => 'hr.payslip_item',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('payslip-items.create');

// Show Route
Route::get('payslip-items/{id}', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::payslip-items.show', [
        'recordId' => (int) $id,
        'configKey' => 'hr.payslip_item',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('payslip-items.show')->where('id', '[0-9]+'); 

// Edit Route
Route::get('payslip-items/{id}/edit', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::payslip-items.edit', [
        'recordId' => (int) $id,
        'configKey' => 'hr.payslip_item',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('payslip-items.edit')->where('id', '[0-9]+'); // And here;


// Routes for PayrollPolicyAssignment

// Create Route
Route::get('payroll-policy-assignments/create', function (\Illuminate\Http\Request $request) {
    return view('hr::payroll-policy-assignments.create', [
        'configKey' => 'hr.payroll_policy_assignment',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('payroll-policy-assignments.create');

// Show Route
Route::get('payroll-policy-assignments/{id}', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::payroll-policy-assignments.show', [
        'recordId' => (int) $id,
        'configKey' => 'hr.payroll_policy_assignment',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('payroll-policy-assignments.show')->where('id', '[0-9]+'); 

// Edit Route
Route::get('payroll-policy-assignments/{id}/edit', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::payroll-policy-assignments.edit', [
        'recordId' => (int) $id,
        'configKey' => 'hr.payroll_policy_assignment',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('payroll-policy-assignments.edit')->where('id', '[0-9]+'); // And here;


// Routes for EmployeePosition

// Create Route
Route::get('employee-positions/create', function (\Illuminate\Http\Request $request) {
    return view('hr::employee-positions.create', [
        'configKey' => 'hr.employee_position',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('employee-positions.create');

// Show Route
Route::get('employee-positions/{id}', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::employee-positions.show', [
        'recordId' => (int) $id,
        'configKey' => 'hr.employee_position',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('employee-positions.show')->where('id', '[0-9]+'); 

// Edit Route
Route::get('employee-positions/{id}/edit', function (\Illuminate\Http\Request $request, $id) {
    return view('hr::employee-positions.edit', [
        'recordId' => (int) $id,
        'configKey' => 'hr.employee_position',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('employee-positions.edit')->where('id', '[0-9]+'); // And here;
