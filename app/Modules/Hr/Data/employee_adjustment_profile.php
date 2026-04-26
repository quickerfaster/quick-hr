<?php

return [
  'model' => 'App\Modules\Hr\Models\EmployeeAdjustmentProfile',
  'fieldDefinitions' => [
    'employee_id' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'livewire-searchable-select',
      'label' => 'Employee',
      'validation' => 'required|exists:employees,id',
      'reactivity' => false,
      'relationship' => [
        'model' => 'App\Modules\Hr\Models\Employee',
        'type' => 'belongsTo',
        'display_field' => 'employee_number',
        'dynamic_property' => 'employee',
        'foreign_key' => 'employee_id',
        'inlineAdd' => false,
      ],
      'options' => [
        'model' => 'App\Modules\Hr\Models\Employee',
        'column' => 'employee_number',
        'hintField' => 'first_name,last_name',
      ],
    ],
    'type' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Adjustment Type',
      'validation' => 'required|in:earning,deduction',
      'options' => [
        'earning' => 'Earning (Addition)',
        'deduction' => 'Deduction (Subtraction)',
      ],
      'reactivity' => false,
    ],
    'label' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Description',
      'validation' => 'required|string|max:255',
      'reactivity' => false,
    ],
    'calculation_type' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Calculation Type',
      'validation' => 'required|in:fixed,percentage',
      'options' => [
        'fixed' => 'Fixed Amount',
        'percentage' => 'Percentage of Base Salary',
      ],
      'reactivity' => false,
    ],
    'value' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'number',
      'label' => 'Value',
      'validation' => 'required|numeric|min:0',
      'reactivity' => false,
    ],
    'effective_date' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'datepicker',
      'label' => 'Effective From',
      'validation' => 'required|date',
      'reactivity' => false,
    ],
    'expiry_date' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'datepicker',
      'label' => 'Expiry Date',
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
    'policy_id' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Source Policy',
      'validation' => 'nullable|exists:payroll_policies,id',
      'reactivity' => false,
      'relationship' => [
        'model' => 'App\Modules\Hr\Models\PayrollPolicy',
        'type' => 'belongsTo',
        'display_field' => 'name',
        'dynamic_property' => 'policy',
        'foreign_key' => 'policy_id',
        'inlineAdd' => false,
      ],
      'options' => [
        'model' => 'App\Modules\Hr\Models\PayrollPolicy',
        'column' => 'name',
        'hintField' => '',
      ],
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
      '0' => 'policy_id',
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
  'viewType' => 'pages',
  'includeControllers' => false,
  'addRoutes' => false,
  'dispatchEvents' => false,
  'controls' => [
    'addButton' => [
      '0' => [
        'label' => 'New Adjustment',
        'type' => 'quick_add',
        'icon' => 'fas fa-plus-circle',
        'primary' => true,
      ],
      '1' => [
        'label' => 'Bulk Import',
        'type' => 'modal',
        'icon' => 'fas fa-file-import',
        'url' => '/hr/employee-adjustment-profiles/import',
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
        'confirm' => 'Activate selected adjustments?',
      ],
      'deactivate' => [
        'label' => 'Deactivate Selected',
        'icon' => 'fas fa-toggle-off',
        'updateModelField' => 'is_active',
        'fieldValue' => false,
        'confirm' => 'Deactivate selected adjustments?',
      ],
      'delete' => true,
    ],
    'perPage' => [
      '0' => 10,
      '1' => 25,
      '2' => 50,
      '3' => 100,
      '4' => 250,
    ],
    'search' => true,
    'showHideColumns' => true,
    'filterColumns' => true,
    'filters' => [
      '0' => [
        'field' => 'type',
        'type' => 'select',
        'options' => [
          '0' => 'All',
          '1' => 'earning',
          '2' => 'deduction',
        ],
        'label' => 'Adjustment Type',
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
      '2' => [
        'field' => 'calculation_type',
        'type' => 'select',
        'options' => [
          '0' => 'All',
          '1' => 'fixed',
          '2' => 'percentage',
        ],
        'label' => 'Calculation Type',
      ],
      '3' => [
        'field' => 'effective_date',
        'type' => 'date_range',
        'label' => 'Effective Date Range',
      ],
      '4' => [
        'field' => 'expiry_date',
        'type' => 'date_range',
        'label' => 'Expiry Date Range',
      ],
    ],
  ],
  'fieldGroups' => [
    'basic_info' => [
      'title' => 'Adjustment Details',
      'groupType' => 'payroll',
      'icon' => 'fas fa-info-circle',
      'fields' => [
        '0' => 'employee_id',
        '1' => 'type',
        '2' => 'label',
        '3' => 'calculation_type',
        '4' => 'value',
      ],
    ],
    'validity' => [
      'title' => 'Validity Period',
      'groupType' => 'payroll',
      'icon' => 'fas fa-calendar-alt',
      'fields' => [
        '0' => 'effective_date',
        '1' => 'expiry_date',
        '2' => 'is_active',
      ],
    ],
    'traceability' => [
      'title' => 'Source & Audit',
      'groupType' => 'payroll',
      'icon' => 'fas fa-history',
      'fields' => [
        '0' => 'policy_id',
        '1' => 'created_by',
        '2' => 'updated_by',
      ],
    ],
  ],
  'moreActions' => [
    '0' => [
      'title' => 'Copy Adjustment',
      'icon' => 'fas fa-copy',
      'route' => 'employee-adjustment-profiles.copy',
      'params' => [
        'id' => '{id}',
      ],
      'confirm' => 'Create a copy of this adjustment?',
      'requiredRole' => [
        '0' => 'hr_admin',
        '1' => 'payroll_officer',
      ],
    ],
    '1' => [
      'title' => 'View Employee Profile',
      'icon' => 'fas fa-user',
      'route' => 'employees.show',
      'params' => [
        'employee_id' => '{employee_id}',
      ],
      'newTab' => true,
    ],
    '2' => [
      'title' => 'View Source Policy',
      'icon' => 'fas fa-gavel',
      'route' => 'payroll-policies.show',
      'params' => [
        'policy_id' => '{policy_id}',
      ],
      'condition' => [
        '0' => [
          'policy_id' => 'not null',
        ],
      ],
    ],
  ],
  'switchViews' => [
    'default' => 'list',
    'card' => [
      'titleFields' => [
        '0' => 'label',
      ],
      'subtitleFields' => [
        '0' => 'type',
        '1' => 'calculation_type',
      ],
      'contentFields' => [
        '0' => 'value',
        '1' => 'effective_date',
      ],
      'badgeField' => 'is_active',
      'badgeColors' => [
        'true' => 'success',
        'false' => 'secondary',
      ],
      'ribbonField' => 'type',
      'ribbonText' => [
        'earning' => 'Earning',
        'deduction' => 'Deduction',
      ],
      'ribbonColor' => [
        'earning' => 'success',
        'deduction' => 'danger',
      ],
    ],
    'list' => [
      'titleFields' => [
        '0' => 'employee.employee_number',
        '1' => 'label',
      ],
      'subtitleFields' => [
        '0' => 'type',
        '1' => 'calculation_type',
      ],
      'contentFields' => [
        '0' => 'value',
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
        '0' => 'label',
      ],
      'subtitleFields' => [
        '0' => 'employee.employee_number',
        '1' => 'type',
      ],
      'tabs' => [
        '0' => [
          'title' => 'Overview',
          'icon' => 'fas fa-info-circle',
          'fields' => [
            '0' => 'employee_id',
            '1' => 'type',
            '2' => 'label',
            '3' => 'calculation_type',
            '4' => 'value',
            '5' => 'effective_date',
            '6' => 'expiry_date',
            '7' => 'is_active',
          ],
        ],
        '1' => [
          'title' => 'Source & Audit',
          'icon' => 'fas fa-history',
          'fields' => [
            '0' => 'policy_id',
            '1' => 'created_by',
            '2' => 'updated_by',
            '3' => 'created_at',
            '4' => 'updated_at',
          ],
        ],
      ],
    ],
  ],
  'relations' => [
    'employee' => [
      'type' => 'belongsTo',
      'model' => 'App\Modules\Hr\Models\Employee',
      'foreignKey' => 'employee_id',
      'localKey' => '',
    ],
    'policy' => [
      'type' => 'belongsTo',
      'model' => 'App\Modules\Hr\Models\PayrollPolicy',
      'foreignKey' => 'policy_id',
      'localKey' => '',
    ],
  ],
  'report' => [],
];
