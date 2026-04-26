<?php

namespace App\Modules\Hr\Http\Controllers;

use App\Modules\Hr\Models\Employee;
use Illuminate\Routing\Controller;

class EmployeePrintController extends Controller
{
    public function show(Employee $employee)
    {
        // Load all necessary relationships (same as your detail page)
        $employee->load([
            'department',
            'employeeProfile',
            'employeePosition.jobTitle',
            'employeePosition.department',
            'employeePosition.manager',
            'employeeWorkPatterns.workPattern',
            //'employeePayrollProfile',
        ]);

        // You can also compute additional data like full name, job title, etc.
        return view('hr::employees.print', compact('employee'));
    }
}
