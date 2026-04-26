<?php

return [
  'model' => 'App\Modules\Hr\Models\EmployeeGroup',
  'fieldDefinitions' => [
    'name' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Group Name',
      'validation' => 'required|string|max:255|unique:employee_groups,name',
      'reactivity' => false,
    ],
    'code' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Group Code',
      'validation' => 'required|string|max:50|unique:employee_groups,code',
      'autoGenerate' => true,
      'reactivity' => false,
    ],
    'description' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'textarea',
      'label' => 'Description',
      'validation' => 'nullable|string|max:1000',
      'reactivity' => false,
    ],
    'group_type' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Group Type',
      'validation' => 'required|in:manual,dynamic,hybrid',
      'options' => [
        'manual' => 'Manual (Admin selects members)',
        'dynamic' => 'Dynamic (Auto‑populated by rules)',
        'hybrid' => 'Hybrid (Manual + Dynamic)',
      ],
      'reactivity' => false,
    ],
    'dynamic_rules' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'json',
      'label' => 'Dynamic Rules (JSON)',
      'validation' => 'nullable|json',
      'reactivity' => false,
    ],
    'is_active' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'checkbox',
      'label' => 'Active',
      'validation' => 'nullable|boolean',
      'reactivity' => false,
    ],
    'created_by' => [
      'display' => 'inline',
      'fillable' => false,
      'field_type' => 'number',
      'label' => 'Created By',
      'validation' => 'nullable|integer',
      'reactivity' => false,
    ],
    'updated_by' => [
      'display' => 'inline',
      'fillable' => false,
      'field_type' => 'number',
      'label' => 'Updated By',
      'validation' => 'nullable|integer',
      'reactivity' => false,
    ],
  ],
  'detailComponent' => '',
  'hiddenFields' => [
    'onTable' => [
      '0' => 'dynamic_rules',
      '1' => 'created_by',
      '2' => 'updated_by',
    ],
    'onNewForm' => [
      '0' => 'created_by',
      '1' => 'updated_by',
    ],
    'onEditForm' => [
      '0' => 'updated_by',
    ],
    'onQuery' => [],
  ],
  'simpleActions' => [
    '0' => 'show',
    '1' => 'edit',
    '2' => 'delete',
  ],
  'isTransaction' => false,
  'viewType' => 'modal',
  'includeControllers' => false,
  'addRoutes' => false,
  'dispatchEvents' => false,
  'controls' => [
    'addButton' => [
      '0' => [
        'label' => 'New Group',
        'type' => 'quick_add',
        'icon' => 'fas fa-plus-circle',
        'primary' => true,
      ],
    ],
    'files' => [
      'export' => [
        '0' => 'xls',
        '1' => 'csv',
        '2' => 'pdf',
      ],
      'print' => true,
    ],
    'bulkActions' => [
      'export' => [
        '0' => 'xls',
        '1' => 'csv',
        '2' => 'pdf',
      ],
      'activate' => [
        'label' => 'Activate Selected',
        'icon' => 'fas fa-toggle-on',
        'updateModelField' => 'is_active',
        'fieldValue' => true,
        'confirm' => 'Activate selected groups?',
      ],
      'deactivate' => [
        'label' => 'Deactivate Selected',
        'icon' => 'fas fa-toggle-off',
        'updateModelField' => 'is_active',
        'fieldValue' => false,
        'confirm' => 'Deactivate selected groups?',
      ],
      'delete' => true,
    ],
    'perPage' => [
      '0' => 10,
      '1' => 25,
      '2' => 50,
      '3' => 100,
    ],
    'search' => true,
    'showHideColumns' => true,
    'filterColumns' => true,
    'filters' => [
      '0' => [
        'field' => 'group_type',
        'type' => 'select',
        'options' => [
          '0' => 'All',
          '1' => 'manual',
          '2' => 'dynamic',
          '3' => 'hybrid',
        ],
        'label' => 'Group Type',
      ],
      '1' => [
        'field' => 'is_active',
        'type' => 'select',
        'options' => [
          '0' => 'All',
          '1' => 'Active',
          '2' => 'Inactive',
        ],
        'label' => 'Status',
        'default' => 'Active',
      ],
    ],
  ],
  'fieldGroups' => [
    'basic_info' => [
      'title' => 'Basic Information',
      'groupType' => 'hr',
      'icon' => 'fas fa-info-circle',
      'fields' => [
        '0' => 'name',
        '1' => 'code',
        '2' => 'description',
        '3' => 'is_active',
      ],
    ],
    'grouping_rules' => [
      'title' => 'Grouping Rules',
      'groupType' => 'hr',
      'icon' => 'fas fa-cogs',
      'fields' => [
        '0' => 'group_type',
        '1' => 'dynamic_rules',
      ],
    ],
    'audit' => [
      'title' => 'Audit',
      'groupType' => 'hr',
      'icon' => 'fas fa-history',
      'fields' => [
        '0' => 'created_by',
        '1' => 'updated_by',
      ],
    ],
  ],
  'moreActions' => [
    '0' => [
      'title' => 'Manage Members',
      'icon' => 'fas fa-user-plus',
      'route' => 'employee-groups.members',
      'params' => [
        'group_id' => '{id}',
      ],
      'requiredRole' => [
        '0' => 'hr_admin',
        '1' => 'manager',
      ],
    ],
    '1' => [
      'title' => 'View Assignments',
      'icon' => 'fas fa-link',
      'route' => 'payroll-policy-assignments.index',
      'params' => [
        'filters[assignable_type]' => 'employee_group',
        'filters[assignable_id]' => '{id}',
      ],
      'newTab' => true,
    ],
    '2' => [
      'title' => 'Sync Dynamic Group',
      'icon' => 'fas fa-sync-alt',
      'dispatchEvent' => true,
      'eventName' => 'syncDynamicGroup',
      'params' => [
        'group_id' => '{id}',
      ],
      'confirm' => 'Re‑evaluate dynamic rules and update membership?',
      'condition' => [
        '0' => [
          'group_type' => [
            '0' => 'dynamic',
            '1' => 'hybrid',
          ],
        ],
      ],
      'requiredRole' => [
        '0' => 'hr_admin',
      ],
    ],
  ],
  'switchViews' => [
    'default' => 'list',
    'card' => [
      'titleFields' => [
        '0' => 'name',
      ],
      'subtitleFields' => [
        '0' => 'code',
        '1' => 'group_type',
      ],
      'contentFields' => [
        '0' => 'description',
      ],
      'badgeField' => 'is_active',
      'badgeColors' => [
        'true' => 'success',
        'false' => 'secondary',
      ],
    ],
    'list' => [
      'titleFields' => [
        '0' => 'name',
      ],
      'subtitleFields' => [
        '0' => 'code',
        '1' => 'group_type',
      ],
      'contentFields' => [
        '0' => 'description',
      ],
      'badgeField' => 'is_active',
      'badgeColors' => [
        'true' => 'success',
        'false' => 'secondary',
      ],
    ],
    'detail' => [
      'layout' => 'tab',
      'detailType' => 'record',
      'titleFields' => [
        '0' => 'name',
      ],
      'subtitleFields' => [
        '0' => 'code',
        '1' => 'group_type',
      ],
      'tabs' => [
        '0' => [
          'title' => 'Overview',
          'icon' => 'fas fa-info-circle',
          'fields' => [
            '0' => 'name',
            '1' => 'code',
            '2' => 'description',
            '3' => 'group_type',
            '4' => 'dynamic_rules',
            '5' => 'is_active',
          ],
        ],
        '1' => [
          'title' => 'Members',
          'icon' => 'fas fa-users',
          'relation' => 'employees',
          'relationLimit' => 50,
        ],
        '2' => [
          'title' => 'Audit',
          'icon' => 'fas fa-history',
          'fields' => [
            '0' => 'created_by',
            '1' => 'updated_by',
            '2' => 'created_at',
            '3' => 'updated_at',
          ],
        ],
      ],
    ],
  ],
  'relations' => [
    'employees' => [
      'type' => 'belongsToMany',
      'model' => 'App\Modules\Hr\Models\Employee',
      'foreignKey' => '',
      'localKey' => '',
    ],
  ],
  'report' => [],
];
