{{-- Sidebar Links for Hr --}}

@include('hr::components.layouts.navbars.auth.payroll/sidebar-pre-links')

{{-- Generated Links --}}
{{-- Generated Links --}}
{{-- Generated Links --}}
{{-- Generated Links --}}
{{-- Generated Links --}}
<li class="nav-item text-nowrap">
<a href="/hr/hr/dashboard-payroll-overview" class="nav-link d-flex align-items-center" data-bs-toggle="tooltip" wire:ignore.self
data-bs-placement="right" title="Overview">
<i class="fas fa-chart-bar me-2"></i>
@if ($state === 'full')
<span>Overview</span>
@endif
</a>
</li>
<li class="nav-item text-nowrap">
<a href="/hr/hr/pay-schedules" class="nav-link d-flex align-items-center" data-bs-toggle="tooltip" wire:ignore.self
data-bs-placement="right" title="Pay Schedules">
<i class="fas fa-calendar-alt me-2"></i>
@if ($state === 'full')
<span>Pay Schedules</span>
@endif
</a>
</li>
<li class="nav-item text-nowrap">
<a href="/hr/hr/employee-payroll-profiles" class="nav-link d-flex align-items-center" data-bs-toggle="tooltip" wire:ignore.self
data-bs-placement="right" title="Payroll Employees">
<i class="fas fa-user-tie me-2"></i>
@if ($state === 'full')
<span>Payroll Employees</span>
@endif
</a>
</li>
<li class="nav-item text-nowrap">
<a href="/hr/hr/payroll-runs" class="nav-link d-flex align-items-center" data-bs-toggle="tooltip" wire:ignore.self
data-bs-placement="right" title="Pay Runs">
<i class="fas fa-file-invoice-dollar me-2"></i>
@if ($state === 'full')
<span>Pay Runs</span>
@endif
</a>
</li>
<li class="nav-item text-nowrap">
    <a href="/hr/hr/payroll-payslips" class="nav-link d-flex align-items-center" data-bs-toggle="tooltip" wire:ignore.self
        data-bs-placement="right" title="Payslips">
        <i class="fas fa-receipt me-2"></i>
        @if ($state === 'full')
            <span>Payslips</span>
        @endif
    </a>
</li>

@include('hr::components.layouts.navbars.auth.payroll/sidebar-post-links')
