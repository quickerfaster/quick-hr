<?php

return [
    'context_groups' => [
        'Users & Permissions' => [
            'label' => 'Users & Permissions',
            'icon' => 'fas fa-users-cog',
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
    ],
    'contexts' => [
        'Users & Permissions' => [
            [
                'key' => 'user',
                'label' => 'Users',
                'icon' => 'fas fa-users-cog',
                'route' => '/admin/users',
                'permission' => 'view_user',
                'order' => 1,
                'page_title' => NULL,
            ],
            [
                'key' => 'role',
                'label' => 'Roles',
                'icon' => 'fas fa-user-shield',
                'route' => '/admin/roles',
                'permission' => 'view_role',
                'order' => 2,
                'page_title' => NULL,
            ],
            [
                'key' => 'permission',
                'label' => 'Assign Permissions',
                'icon' => 'fas fa-user-lock',
                'route' => '/admin/access-control-management',
                'permission' => 'view_permission',
                'order' => 3,
                'page_title' => NULL,
            ],
            [
                'key' => 'assign_user_role',
                'label' => 'Assign Roles',
                'icon' => 'fas fa-user-tag',
                'route' => '/admin/role-assignment',
                'permission' => 'view_assign_user_role',
                'order' => 4,
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
