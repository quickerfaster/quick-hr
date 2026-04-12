<?php

return [
    'context_groups' => [
        'Users & Permissions' => [
            'label' => 'Users & Permissions',
            'icon' => 'fas fa-user-cog',
            'order' => 2,
            'route' => NULL,
            'url' => 'admin/users',
        ],
        'audit' => [
            'label' => 'Audit',
            'icon' => 'fas fa-history',
            'order' => 10000,
            'route' => NULL,
            'url' => 'admin/activity-logs',
        ],
        'General Settings' => [
            'label' => 'General Settings',
            'icon' => 'fas fa-cogs',
            'order' => 10000,
            'route' => NULL,
            'url' => 'admin/general-settings',
        ],
        'Company Profile' => [
            'label' => 'Company Profile',
            'icon' => 'fas fa-building',
            'order' => 999,
            'route' => NULL,
            'url' => 'admin/dashboard-company-profile-overview',
        ],
    ],
    'contexts' => [
        'Users & Permissions' => [
            [
                'key' => 'user',
                'label' => 'Users',
                'icon' => 'fas fa-user-cog',
                'route' => '/admin/users',
                'permission' => 'view_user',
                'order' => 999,
                'page_title' => NULL,
            ],
            [
                'key' => 'role',
                'label' => 'Roles',
                'icon' => 'fas fa-user-shield',
                'route' => '/admin/roles',
                'permission' => 'view_role',
                'order' => 999,
                'page_title' => NULL,
            ],
            [
                'key' => 'permission',
                'label' => 'Permissions',
                'icon' => 'fas fa-user-lock',
                'route' => '/admin/access-control-management',
                'permission' => 'view_permission',
                'order' => 999,
                'page_title' => NULL,
            ],
        ],
        'audit' => [
            [
                'key' => 'activity_log',
                'label' => 'Activity Log',
                'icon' => 'fas fa-history',
                'route' => '/admin/activity-logs',
                'permission' => 'view_activity_log',
                'order' => 10,
                'page_title' => 'Activity Log',
            ],
        ],
        'Company Profile' => [
            [
                'key' => 'overview',
                'label' => 'Overview',
                'icon' => 'fas fa-chart-bar',
                'route' => '/admin/dashboard-company-profile-overview',
                'permission' => 'view_overview',
                'order' => 1,
                'page_title' => NULL,
            ],
            [
                'key' => 'location',
                'label' => 'Locations',
                'icon' => 'fas fa-map-marker-alt',
                'route' => '/admin/locations',
                'permission' => 'view_location',
                'order' => 999,
                'page_title' => NULL,
            ],
            [
                'key' => 'company',
                'label' => 'Companies',
                'icon' => 'fas fa-building',
                'route' => '/admin/companies',
                'permission' => 'manage-system',
                'order' => 999,
                'page_title' => NULL,
            ],
            [
                'key' => 'department',
                'label' => 'Departments',
                'icon' => 'fas fa-sitemap',
                'route' => '/admin/departments',
                'permission' => 'view_department',
                'order' => 999,
                'page_title' => NULL,
            ],
            [
                'key' => 'job_title',
                'label' => 'Job Titles',
                'icon' => 'fas fa-briefcase',
                'route' => '/admin/job-titles',
                'permission' => 'view_job_title',
                'order' => 999,
                'page_title' => NULL,
            ],
        ],
        'General Settings' => [
            [
                'key' => 'system_setting',
                'label' => 'System Settings',
                'icon' => 'fas fa-user',
                'route' => '/system-settings',
                'permission' => 'view_system_setting',
                'order' => 999,
                'page_title' => NULL,
            ],
        ],
    ],
    'layout' => [
        'top_bar' => [
            'enabled' => true,
        ],
        'context_menu' => [
            'type' => 'sidebar',
            'position' => 'left',
            'allow_switch' => true,
            'default_type' => 'sidebar',
        ],
        'sidebar' => [
            'initial_state' => 'full',
        ],
        'bottom_bar' => [
            'enabled' => true,
        ],
        'breadcrumb' => [
            'enabled' => true,
        ],
        'title' => [
            'enabled' => true,
        ],
    ],
    'shared_items' => [
        'header' => [],
        'footer' => [],
    ],
    'shared_top_items' => [
        'left' => [],
        'right' => [],
    ],
];
