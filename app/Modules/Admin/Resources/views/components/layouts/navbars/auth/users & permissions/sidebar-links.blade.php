{{-- Sidebar Links for Admin --}}

@include('admin::components.layouts.navbars.auth.users & permissions/sidebar-pre-links')

{{-- Generated Links --}}
{{-- Generated Links --}}
{{-- Generated Links --}}
{{-- Generated Links --}}
<li class="nav-item text-nowrap">
<a href="/admin/admin/users" class="nav-link d-flex align-items-center" data-bs-toggle="tooltip" wire:ignore.self
data-bs-placement="right" title="Users">
<i class="fas fa-users-cog me-2"></i>
@if ($state === 'full')
<span>Users</span>
@endif
</a>
</li>
<li class="nav-item text-nowrap">
<a href="/admin/admin/roles" class="nav-link d-flex align-items-center" data-bs-toggle="tooltip" wire:ignore.self
data-bs-placement="right" title="Roles">
<i class="fas fa-user-shield me-2"></i>
@if ($state === 'full')
<span>Roles</span>
@endif
</a>
</li>
<li class="nav-item text-nowrap">
<a href="/admin/admin/access-control-management" class="nav-link d-flex align-items-center" data-bs-toggle="tooltip" wire:ignore.self
data-bs-placement="right" title="Assign Permissions">
<i class="fas fa-user-lock me-2"></i>
@if ($state === 'full')
<span>Assign Permissions</span>
@endif
</a>
</li>
<li class="nav-item text-nowrap">
    <a href="/admin/admin/role-assignment" class="nav-link d-flex align-items-center" data-bs-toggle="tooltip" wire:ignore.self
        data-bs-placement="right" title="Assign Roles">
        <i class="fas fa-user-tag me-2"></i>
        @if ($state === 'full')
            <span>Assign Roles</span>
        @endif
    </a>
</li>

@include('admin::components.layouts.navbars.auth.users & permissions/sidebar-post-links')
