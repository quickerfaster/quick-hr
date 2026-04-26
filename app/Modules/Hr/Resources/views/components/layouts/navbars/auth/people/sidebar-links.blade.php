{{-- Sidebar Links for Hr --}}

@include('hr::components.layouts.navbars.auth.people/sidebar-pre-links')

{{-- Generated Links --}}
{{-- Generated Links --}}
{{-- Generated Links --}}
{{-- Generated Links --}}
{{-- Generated Links --}}
{{-- Generated Links --}}
{{-- Generated Links --}}
<li class="nav-item text-nowrap">
<a href="/hr/hr/dashboard-people-overview" class="nav-link d-flex align-items-center" data-bs-toggle="tooltip" wire:ignore.self
data-bs-placement="right" title="Overview">
<i class="fas fa-chart-bar me-2"></i>
@if ($state === 'full')
<span>Overview</span>
@endif
</a>
</li>
<li class="nav-item text-nowrap">
<a href="/hr/hr/employees" class="nav-link d-flex align-items-center" data-bs-toggle="tooltip" wire:ignore.self
data-bs-placement="right" title="Employees">
<i class="fas fa-user-friends me-2"></i>
@if ($state === 'full')
<span>Employees</span>
@endif
</a>
</li>
<li class="nav-item text-nowrap">
<a href="/hr/hr/employee-job-histories" class="nav-link d-flex align-items-center" data-bs-toggle="tooltip" wire:ignore.self
data-bs-placement="right" title="Job History">
<i class="fas fa-history me-2"></i>
@if ($state === 'full')
<span>Job History</span>
@endif
</a>
</li>
<li class="nav-item text-nowrap">
<a href="/hr/hr/employee-profiles" class="nav-link d-flex align-items-center" data-bs-toggle="tooltip" wire:ignore.self
data-bs-placement="right" title="Profiles">
<i class="fas fa-user-circle me-2"></i>
@if ($state === 'full')
<span>Profiles</span>
@endif
</a>
</li>
<li class="nav-item text-nowrap">
<a href="/hr/hr/employee-groups" class="nav-link d-flex align-items-center" data-bs-toggle="tooltip" wire:ignore.self
data-bs-placement="right" title="Employee Groups">
<i class="fas fa-users me-2"></i>
@if ($state === 'full')
<span>Employee Groups</span>
@endif
</a>
</li>
<li class="nav-item text-nowrap">
<a href="/hr/hr/documents" class="nav-link d-flex align-items-center" data-bs-toggle="tooltip" wire:ignore.self
data-bs-placement="right" title="Documents">
<i class="fas fa-file me-2"></i>
@if ($state === 'full')
<span>Documents</span>
@endif
</a>
</li>
<li class="nav-item text-nowrap">
    <a href="/hr/hr/employee-positions" class="nav-link d-flex align-items-center" data-bs-toggle="tooltip" wire:ignore.self
        data-bs-placement="right" title="Current Job">
        <i class="fas fa-briefcase me-2"></i>
        @if ($state === 'full')
            <span>Current Job</span>
        @endif
    </a>
</li>

@include('hr::components.layouts.navbars.auth.people/sidebar-post-links')
