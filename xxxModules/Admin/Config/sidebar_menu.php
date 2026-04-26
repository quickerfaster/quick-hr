<?php

return [
    [
    'title' => 'Users',
    'icon' => 'fas fa-user-cog',
    'url' => 'admin/admin/users',
    'permission' => 'view_user',
],
    [
    'title' => 'Roles',
    'icon' => 'fas fa-user-shield',
    'url' => 'admin/admin/roles',
    'permission' => 'view_role',
],
    [
    'title' => 'Permissions',
    'icon' => 'fas fa-user-lock',
    'url' => 'admin/admin/access-control-management',
    'permission' => 'view_permission',
],
    [
    'itemType' => 'item-separator',
    'title' => '<h6 class="ps-3 mt-4 mb-2 text-uppercase text-xs font-weight-bolder opacity-6 group-title">Audit</h6>',
    'url' => null,
],
    [
    'title' => 'Activity Log',
    'icon' => 'fas fa-history',
    'url' => 'admin/admin/activity-logs',
    'permission' => 'view_activity_log',
    'groupTitle' => 'Audit',
    'order' => 10,
],
    [
    'title' => 'Overview',
    'icon' => 'fas fa-chart-bar',
    'url' => 'admin/admin/dashboard-company-profile-overview',
    'permission' => 'view_overview',
    'order' => 1,
],
    [
    'title' => 'System Settings',
    'icon' => 'fas fa-cube',
    'url' => 'admin/system-settings',
    'permission' => 'view_system_setting',
],
    [
    'title' => 'Locations',
    'icon' => 'fas fa-map-marker-alt',
    'url' => 'admin/admin/locations',
    'permission' => 'view_location',
],
    [
    'itemType' => 'item-separator',
    'title' => '<h6 class="ps-3 mt-4 mb-2 text-uppercase text-xs font-weight-bolder opacity-6 group-title">Settings</h6>',
    'url' => null,
],
    [
    'title' => 'Companies',
    'icon' => 'fas fa-building',
    'url' => 'admin/admin/companies',
    'permission' => 'manage-system',
    'groupTitle' => 'Settings',
],
    [
    'itemType' => 'item-separator',
    'title' => '<h6 class="ps-3 mt-4 mb-2 text-uppercase text-xs font-weight-bolder opacity-6 group-title">Organization</h6>',
    'url' => null,
],
    [
    'title' => 'Departments',
    'icon' => 'fas fa-sitemap',
    'url' => 'admin/admin/departments',
    'permission' => 'view_department',
    'groupTitle' => 'Organization',
],
    [
    'title' => 'Job Titles',
    'icon' => 'fas fa-briefcase',
    'url' => 'admin/admin/job-titles',
    'permission' => 'view_job_title',
    'groupTitle' => 'Organization',
],
];
