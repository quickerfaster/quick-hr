<?php

return [
  'model' => 'App\Modules\Hr\Models\PayrollPolicyAssignment',
  'fieldDefinitions' => [
    'payroll_policy_id' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Payroll Policy',
      'validation' => 'required|exists:payroll_policies,id',
      'reactivity' => false,
      'relationship' => [
        'model' => 'App\Modules\Hr\Models\PayrollPolicy',
        'type' => 'belongsTo',
        'display_field' => 'name',
        'dynamic_property' => 'payrollPolicy',
        'foreign_key' => 'payroll_policy_id',
        'inlineAdd' => false,
      ],
      'options' => [
        'model' => 'App\Modules\Hr\Models\PayrollPolicy',
        'column' => 'name',
        'hintField' => '',
      ],
    ],
    'assignable_type' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Assign to Type',
      'options' => [
        'company' => 'Company',
        'location' => 'Location',
        'department' => 'Department',
        'shift' => 'Shift',
        'employee_group' => 'Employee Group',
      ],
      'reactivity' => false,
    ],
    'assignable_id' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'morph_to_select',
      'label' => 'Assign to Entity',
      'reactivity' => false,
      'morph_relation' => 'assignable',
      'morph_map' => [
        'company' => 'App\Modules\Admin\Models\Company',
        'location' => 'App\Modules\Admin\Models\Location',
        'department' => 'App\Modules\Admin\Models\Department',
        'shift' => 'App\Modules\Admin\Models\Shift',
        'employee_group' => 'App\Modules\Hr\Models\EmployeeGroup',
      ],
      'display_field' => 'name',
    ],
    'priority' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'number',
      'label' => 'Priority',
      'validation' => 'integer|min:0',
      'reactivity' => false,
    ],
    'effective_date' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'datepicker',
      'label' => 'Assignment Effective From',
      'validation' => 'nullable|date',
      'reactivity' => false,
    ],
    'expiry_date' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'datepicker',
      'label' => 'Assignment Expiry Date',
      'validation' => 'nullable|date|after:effective_date',
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
      '0' => 'created_by',
      '1' => 'updated_by',
      '2' => 'effective_date',
      '3' => 'expiry_date',
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
  'viewType' => 'pages',
  'includeControllers' => false,
  'addRoutes' => false,
  'dispatchEvents' => false,
  'controls' => [
    'addButton' => [
      '0' => [
        'label' => 'New Assignment',
        'type' => 'quick_add',
        'icon' => 'fas fa-plus-circle',
        'primary' => true,
      ],
      '1' => [
        'label' => 'Bulk Assign',
        'type' => 'modal',
        'icon' => 'fas fa-layer-group',
        'url' => '/hr/payroll-policy-assignments/bulk',
        'modalSize' => 'lg',
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
        'confirm' => 'Activate selected assignments?',
      ],
      'deactivate' => [
        'label' => 'Deactivate Selected',
        'icon' => 'fas fa-toggle-off',
        'updateModelField' => 'is_active',
        'fieldValue' => false,
        'confirm' => 'Deactivate selected assignments?',
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
        'field' => 'assignable_type',
        'type' => 'select',
        'options' => [
          '0' => 'All',
          '1' => 'company',
          '2' => 'location',
          '3' => 'department',
          '4' => 'shift',
          '5' => 'employee_group',
        ],
        'label' => 'Entity Type',
      ],
      '1' => [
        'field' => 'payroll_policy_id',
        'type' => 'select',
        'optionsFrom' => 'payroll_policies',
        'label' => 'Policy',
      ],
      '2' => [
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
      '3' => [
        'field' => 'priority',
        'type' => 'number_range',
        'label' => 'Priority Range',
      ],
      '4' => [
        'field' => 'effective_date',
        'type' => 'date_range',
        'label' => 'Effective Date Range',
      ],
    ],
  ],
  'fieldGroups' => [
    'assignment_info' => [
      'title' => 'Assignment Details',
      'groupType' => 'payroll',
      'icon' => 'fas fa-link',
      'fields' => [
        '0' => 'payroll_policy_id',
        '1' => 'assignable_type',
        '2' => 'assignable_id',
        '3' => 'priority',
      ],
    ],
    'validity' => [
      'title' => 'Validity Overrides',
      'groupType' => 'payroll',
      'icon' => 'fas fa-calendar-alt',
      'fields' => [
        '0' => 'effective_date',
        '1' => 'expiry_date',
        '2' => 'is_active',
      ],
    ],
    'audit' => [
      'title' => 'Audit',
      'groupType' => 'payroll',
      'icon' => 'fas fa-history',
      'fields' => [
        '0' => 'created_by',
        '1' => 'updated_by',
      ],
    ],
  ],
  'moreActions' => [
    '0' => [
      'title' => 'View Policy',
      'icon' => 'fas fa-gavel',
      'route' => 'payroll-policies.show',
      'params' => [
        'payroll_policy' => '{payroll_policy_id}',
      ],
      'newTab' => true,
    ],
    '1' => [
      'title' => 'View Entity',
      'icon' => 'fas fa-eye',
      'route' => 'dynamic',
      'dynamicRoute' => true,
      'condition' => [
        '0' => [
          'assignable_type' => 'not null',
        ],
      ],
      'newTab' => true,
    ],
    '2' => [
      'title' => 'Copy Assignment',
      'icon' => 'fas fa-copy',
      'route' => 'payroll-policy-assignments.copy',
      'params' => [
        'id' => '{id}',
      ],
      'confirm' => 'Create a copy of this assignment?',
      'requiredRole' => [
        '0' => 'hr_admin',
        '1' => 'payroll_officer',
      ],
    ],
  ],
  'switchViews' => [
    'default' => 'list',
    'card' => [
      'titleFields' => [
        '0' => 'payrollPolicy.name',
      ],
      'subtitleFields' => [
        '0' => 'assignable_type',
        '1' => 'priority',
      ],
      'contentFields' => [
        '0' => 'assignable.name',
        '1' => 'is_active',
      ],
      'badgeField' => 'is_active',
      'badgeColors' => [
        'true' => 'success',
        'false' => 'secondary',
      ],
      'ribbonField' => 'assignable_type',
      'ribbonText' => [
        'company' => 'Company',
        'location' => 'Location',
        'department' => 'Dept',
        'shift' => 'Shift',
        'employee_group' => 'Group',
      ],
    ],
    'list' => [
      'titleFields' => [
        '0' => 'payrollPolicy.name',
      ],
      'subtitleFields' => [
        '0' => 'assignable_type',
        '1' => 'assignable.name',
      ],
      'contentFields' => [
        '0' => 'priority',
        '1' => 'effective_date',
        '2' => 'expiry_date',
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
        '0' => 'payrollPolicy.name',
      ],
      'subtitleFields' => [
        '0' => 'assignable_type',
        '1' => 'assignable.name',
      ],
      'tabs' => [
        '0' => [
          'title' => 'Overview',
          'icon' => 'fas fa-info-circle',
          'fields' => [
            '0' => 'payroll_policy_id',
            '1' => 'assignable_type',
            '2' => 'assignable_id',
            '3' => 'priority',
            '4' => 'is_active',
          ],
        ],
        '1' => [
          'title' => 'Validity',
          'icon' => 'fas fa-calendar-alt',
          'fields' => [
            '0' => 'effective_date',
            '1' => 'expiry_date',
          ],
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
    'payrollPolicy' => [
      'type' => 'belongsTo',
      'model' => 'App\Modules\Hr\Models\PayrollPolicy',
      'foreignKey' => 'payroll_policy_id',
      'localKey' => '',
    ],
    'assignable' => [
      'type' => 'morphTo',
      'model' => '',
      'foreignKey' => '',
      'localKey' => '',
      'typeField' => 'assignable_type',
      'idField' => 'assignable_id',
      'morphMap' => [
        'company' => 'App\Modules\Admin\Models\Company',
        'location' => 'App\Modules\Admin\Models\Location',
        'department' => 'App\Modules\Admin\Models\Department',
        'shift' => 'App\Modules\Admin\Models\Shift',
        'employee_group' => 'App\Modules\Hr\Models\EmployeeGroup',
      ],
      'displayField' => 'name',
    ],
  ],
  'report' => [],
];
