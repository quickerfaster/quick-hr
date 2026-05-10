<?php

return [
  'model' => 'App\Modules\Hr\Models\PayrollPolicy',
  'fieldDefinitions' => [
    'name' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Policy Name',
      'validation' => 'required|string|max:255|unique:payroll_policies,name',
      'searchable' => true,
    ],
    'type' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Policy Type',
      'validation' => 'required|in:tax,pension,insurance,benefit,bonus,deduction',
      'options' => [
        'tax' => 'Tax',
        'pension' => 'Pension',
        'insurance' => 'Insurance',
        'benefit' => 'Benefit',
        'bonus' => 'Bonus',
        'deduction' => 'Deduction',
      ],
      'filterable' => true,
    ],
    'effect' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Effect on Pay',
      'validation' => 'required|in:addition,subtraction',
      'options' => [
        'addition' => 'Addition (adds to gross pay)',
        'subtraction' => 'Subtraction (deducts from gross pay)',
      ],
    ],
    'country_code' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Country',
      'validation' => 'required|string|size:2',
      'options' => [
        'US' => 'US',
        'UK' => 'UK',
        'NG' => 'NG',
      ],
    ],
    'state_code' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'State/Province',
      'validation' => 'nullable|string|max:10',
    ],
    'calculation_logic' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'textarea',
      'label' => 'Calculation Logic (JSON)',
      'validation' => 'nullable|json',
    ],
    'employer_ratio' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'number',
      'label' => 'Employer Contribution (%)',
      'validation' => 'nullable|numeric|min:0|max:100',
    ],
    'is_statutory' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'checkbox',
      'label' => 'Statutory (Government Mandated)',
      'validation' => 'nullable|boolean',
    ],
    'effective_date' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'datepicker',
      'label' => 'Effective From',
      'validation' => 'required|date',
    ],
    'expiry_date' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'datepicker',
      'label' => 'Expiry Date',
      'validation' => 'nullable|date|after:effective_date',
    ],
    'is_active' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'checkbox',
      'label' => 'Active',
      'validation' => 'nullable|boolean',
    ],
    'description' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'textarea',
      'label' => 'Description',
      'validation' => 'nullable|string|max:1000',
    ],
    'parent_policy_id' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Parent Policy',
      'validation' => 'nullable|exists:payroll_policies,id',
      'relationship' => [
        'model' => 'App\Modules\Hr\Models\PayrollPolicy',
        'type' => 'belongsTo',
        'display_field' => 'name',
        'dynamic_property' => 'parentPolicy',
        'foreign_key' => 'parent_policy_id',
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
    ],
    'updated_by' => [
      'display' => 'inline',
      'fillable' => false,
      'field_type' => 'number',
      'label' => 'Updated By',
      'validation' => 'nullable|integer',
    ],
  ],
  'detailComponent' => '',
  'hiddenFields' => [
    'onTable' => [
      '0' => 'parent_policy_id',
      '1' => 'created_by',
      '2' => 'updated_by',
      '3' => 'description',
    ],
    'onNewForm' => [
      '0' => 'created_by',
      '1' => 'updated_by',
      '2' => 'calculation_logic',
    ],
    'onEditForm' => [
      '0' => 'updated_by',
      '1' => 'calculation_logic',
    ],
    'onQuery' => [],
  ],
  'simpleActions' => [
    '0' => 'show',
    '1' => 'edit',
    '2' => 'delete',
  ],
  'isTransaction' => false,
  'crudType' => 'pages',
  'includeControllers' => false,
  'tableDefaultFields' => [],
  'addRoutes' => false,
  'dispatchEvents' => false,
  'controls' => [
    'addButton' => [
      '0' => [
        'label' => 'New Policy',
        'type' => 'quick_add',
        'icon' => 'fas fa-plus-circle',
        'primary' => true,
      ],
      '1' => [
        'label' => 'Copy from Existing',
        'type' => 'modal',
        'icon' => 'fas fa-copy',
        'url' => '/hr/payroll-policies/copy',
        'modalSize' => 'lg',
      ],
      '2' => [
        'label' => 'Import Policies',
        'type' => 'modal',
        'icon' => 'fas fa-file-import',
        'url' => '/hr/payroll-policies/import',
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
        'confirm' => 'Activate selected policies?',
      ],
      'deactivate' => [
        'label' => 'Deactivate Selected',
        'icon' => 'fas fa-toggle-off',
        'updateModelField' => 'is_active',
        'fieldValue' => false,
        'confirm' => 'Deactivate selected policies?',
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
          '1' => 'tax',
          '2' => 'pension',
          '3' => 'insurance',
          '4' => 'benefit',
          '5' => 'bonus',
          '6' => 'deduction',
        ],
        'label' => 'Policy Type',
      ],
      '1' => [
        'field' => 'country_code',
        'type' => 'select',
        'optionsFrom' => 'countries',
        'label' => 'Country',
      ],
      '2' => [
        'field' => 'state_code',
        'type' => 'select',
        'optionsFrom' => 'states',
        'label' => 'State/Province',
      ],
      '3' => [
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
      '4' => [
        'field' => 'is_statutory',
        'type' => 'select',
        'options' => [
          '0' => 'All',
          '1' => 'Statutory',
          '2' => 'Discretionary',
        ],
        'label' => 'Legal Requirement',
      ],
      '5' => [
        'field' => 'effective_date',
        'type' => 'date_range',
        'label' => 'Effective Date Range',
      ],
    ],
  ],
  'fieldGroups' => [
    'basic_info' => [
      'title' => 'Policy Information',
      'groupType' => 'payroll',
      'icon' => 'fas fa-info-circle',
      'fields' => [
        '0' => 'name',
        '1' => 'type',
        '2' => 'is_statutory',
        '3' => 'description',
      ],
    ],
    'jurisdiction' => [
      'title' => 'Jurisdiction',
      'groupType' => 'payroll',
      'icon' => 'fas fa-globe',
      'fields' => [
        '0' => 'country_code',
        '1' => 'state_code',
      ],
    ],
    'calculation' => [
      'title' => 'Calculation Rules',
      'groupType' => 'payroll',
      'icon' => 'fas fa-calculator',
      'fields' => [
        '0' => 'calculation_logic',
        '1' => 'employer_ratio',
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
    'hierarchy' => [
      'title' => 'Policy Hierarchy',
      'groupType' => 'payroll',
      'icon' => 'fas fa-sitemap',
      'fields' => [
        '0' => 'parent_policy_id',
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
      'title' => 'Copy Policy',
      'icon' => 'fas fa-copy',
      'route' => 'payroll-policies.copy',
      'params' => [
        'id' => '{id}',
      ],
      'confirm' => 'Create a copy of this policy?',
      'requiredRole' => [
        '0' => 'hr_admin',
        '1' => 'payroll_officer',
      ],
    ],
    '1' => [
      'title' => 'View Assignments',
      'icon' => 'fas fa-link',
      'route' => 'payroll-policy-assignments.index',
      'params' => [
        'filters[payroll_policy_id]' => '{id}',
      ],
      'newTab' => true,
    ],
    '2' => [
      'title' => 'View Child Policies',
      'icon' => 'fas fa-sitemap',
      'route' => 'payroll-policies.index',
      'params' => [
        'filters[parent_policy_id]' => '{id}',
      ],
      'condition' => [
        '0' => [
          'has_children' => true,
        ],
      ],
    ],
    '3' => [
      'title' => 'View Usage Report',
      'icon' => 'fas fa-chart-line',
      'dispatchEvent' => true,
      'eventName' => 'openPolicyUsageReport',
      'params' => [
        'policy_id' => '{id}',
      ],
      'requiredRole' => [
        '0' => 'admin',
        '1' => 'hr_admin',
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
        '0' => 'type',
        '1' => 'country_code',
      ],
      'contentFields' => [
        '0' => 'effective_date',
        '1' => 'is_statutory',
      ],
      'badgeField' => 'is_active',
      'badgeColors' => [
        'true' => 'success',
        'false' => 'secondary',
      ],
      'ribbonField' => 'type',
      'ribbonText' => [
        'tax' => 'Tax',
        'pension' => 'Pension',
        'insurance' => 'Insurance',
        'benefit' => 'Benefit',
        'bonus' => 'Bonus',
        'deduction' => 'Deduction',
      ],
      'ribbonColor' => [
        'tax' => 'danger',
        'pension' => 'info',
        'insurance' => 'primary',
        'benefit' => 'success',
        'bonus' => 'warning',
        'deduction' => 'secondary',
      ],
    ],
    'list' => [
      'titleFields' => [
        '0' => 'name',
      ],
      'subtitleFields' => [
        '0' => 'type',
        '1' => 'country_code',
        '2' => 'state_code',
      ],
      'contentFields' => [
        '0' => 'effective_date',
        '1' => 'employer_ratio',
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
        '0' => 'type',
        '1' => 'country_code',
        '2' => 'state_code',
      ],
      'tabs' => [
        '0' => [
          'title' => 'Overview',
          'icon' => 'fas fa-info-circle',
          'fields' => [
            '0' => 'name',
            '1' => 'type',
            '2' => 'country_code',
            '3' => 'state_code',
            '4' => 'is_statutory',
            '5' => 'description',
          ],
        ],
        '1' => [
          'title' => 'Calculation',
          'icon' => 'fas fa-calculator',
          'fields' => [
            '0' => 'calculation_logic',
            '1' => 'employer_ratio',
          ],
        ],
        '2' => [
          'title' => 'Validity',
          'icon' => 'fas fa-calendar-alt',
          'fields' => [
            '0' => 'effective_date',
            '1' => 'expiry_date',
            '2' => 'is_active',
          ],
        ],
        '3' => [
          'title' => 'Hierarchy',
          'icon' => 'fas fa-sitemap',
          'fields' => [
            '0' => 'parent_policy_id',
          ],
        ],
        '4' => [
          'title' => 'Assignments',
          'icon' => 'fas fa-link',
          'relation' => 'assignments',
          'relationLimit' => 20,
        ],
        '5' => [
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
    'parentPolicy' => [
      'type' => 'belongsTo',
      'model' => 'App\Modules\Hr\Models\PayrollPolicy',
      'foreignKey' => 'parent_policy_id',
      'localKey' => '',
    ],
    'childPolicies' => [
      'type' => 'hasMany',
      'model' => 'App\Modules\Hr\Models\PayrollPolicy',
      'foreignKey' => 'parent_policy_id',
      'localKey' => '',
    ],
    'assignments' => [
      'type' => 'hasMany',
      'model' => 'App\Modules\Hr\Models\PayrollPolicyAssignment',
      'foreignKey' => 'payroll_policy_id',
      'localKey' => '',
    ],
  ],
  'report' => [],
];
