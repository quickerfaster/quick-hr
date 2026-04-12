<?php

use Illuminate\Support\Facades\Route;



use App\Modules\Hr\Http\Controllers\PayrollRunController;
use App\Modules\Hr\Http\Controllers\PayrollReportController;
use App\Modules\Hr\Http\Controllers\PayslipController;

use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use App\Modules\Hr\Http\Livewire\AdjustAttendanceMvp;



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
