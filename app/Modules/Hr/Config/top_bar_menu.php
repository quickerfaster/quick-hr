<?php

return [
    [
    'title' => 'Policies',
    'icon' => 'fas fa-gavel',
    'url' => 'hr/hr/dashboard-policies-overview',
    'permission' => 'view_attendance_policy',
],
    [
    'title' => 'Report',
    'icon' => 'fas fa-file-alt',
    'url' => 'hr/reports',
    'permission' => 'view_saved_report',
],
    [
    'title' => 'Employees',
    'icon' => 'fas fa-user-tie',
    'url' => 'hr/hr/dashboard-people-overview',
    'permission' => 'view_employee',
],
    [
    'title' => 'Overview',
    'icon' => 'fas fa-file-invoice-dollar',
    'url' => 'hr/hr/dashboard-payroll-overview',
    'permission' => 'view_payroll_overview',
],
    [
    'title' => 'Leave',
    'icon' => 'fas fa-user-check',
    'url' => 'hr/hr/dashboard-leave-overview',
    'permission' => 'view_leave_request',
],
    [
    'title' => 'Attendance',
    'icon' => 'fas fa-user-clock',
    'url' => 'hr/hr/dashboard-time-overview',
    'permission' => 'view_attendance',
],
];
