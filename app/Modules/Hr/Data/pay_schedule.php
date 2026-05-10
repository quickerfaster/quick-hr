<?php

return [
  'model' => 'App\Modules\Hr\Models\PaySchedule',
  'fieldDefinitions' => [
    'name' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Schedule Name',
      'validation' => 'required|string|max:255|unique:pay_schedules,name',
      'searchable' => true,
    ],
    'code' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Schedule Code',
      'validation' => 'required|string|max:50|unique:pay_schedules,code',
      'autoGenerate' => true,
    ],
    'frequency' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Pay Frequency',
      'validation' => 'required|in:weekly,biweekly,semi_monthly,monthly,quarterly,yearly',
      'options' => [
        'weekly' => 'Weekly (52 pay periods per year)',
        'biweekly' => 'Bi-weekly (26 pay periods per year)',
        'semi_monthly' => 'Semi-monthly (24 pay periods per year)',
        'monthly' => 'Monthly (12 pay periods per year)',
        'quarterly' => 'Quarterly (4 pay periods per year)',
        'yearly' => 'Yearly (1 pay period per year)',
      ],
      'filterable' => true,
    ],
    'first_period_start_date' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'datepicker',
      'label' => 'First Period Start Date',
      'validation' => 'required|date',
    ],
    'next_pay_date' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'datepicker',
      'label' => 'Next Pay Date',
      'validation' => 'required|date|after_or_equal:first_period_start_date',
    ],
    'payment_delay_days' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'number',
      'label' => 'Payment Delay (Days)',
      'validation' => 'nullable|integer|min:0|max:30',
    ],
    'country_code' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Default Country',
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
      'label' => 'Default State/Province',
      'validation' => 'nullable|string|max:10',
      'options' => [
        'US' => 'US',
        'UK' => 'UK',
        'NG' => 'NG',
      ],
    ],
    'currency_code' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Default Currency',
      'validation' => 'required|string|size:3',
      'options' => [
        'USD' => 'USD',
        'BP' => 'BP',
        'NGN' => 'NGN',
      ],
    ],
    'timezone' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Timezone',
      'validation' => 'required|string|max:64',
      'options' => [
        'America/New_York' => 'America/New_York',
        'Europe/London' => 'Europe/London',
        'Africa/Lagos' => 'Africa/Lagos',
        '' => '',
      ],
    ],
    'is_active' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'checkbox',
      'label' => 'Active',
      'validation' => 'nullable|boolean',
    ],
    'is_default' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'checkbox',
      'label' => 'Default Schedule',
      'validation' => 'nullable|boolean',
    ],
    'description' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'textarea',
      'label' => 'Description',
      'validation' => 'nullable|string|max:1000',
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
      '0' => 'first_period_start_date',
      '1' => 'payment_delay_days',
      '2' => 'created_by',
      '3' => 'updated_by',
      '4' => 'description',
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
  'crudType' => 'pages',
  'includeControllers' => false,
  'tableDefaultFields' => [],
  'addRoutes' => false,
  'dispatchEvents' => false,
  'controls' => [
    'addButton' => [
      '0' => [
        'label' => 'New Schedule',
        'type' => 'quick_add',
        'icon' => 'fas fa-plus-circle',
        'primary' => true,
      ],
      '1' => [
        'label' => 'Copy Schedule',
        'type' => 'modal',
        'icon' => 'fas fa-copy',
        'url' => '/hr/pay-schedules/copy',
        'modalSize' => 'md',
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
        'confirm' => 'Activate selected pay schedules?',
      ],
      'deactivate' => [
        'label' => 'Deactivate Selected',
        'icon' => 'fas fa-toggle-off',
        'updateModelField' => 'is_active',
        'fieldValue' => false,
        'confirm' => 'Deactivate selected pay schedules?',
      ],
      'set_default' => [
        'label' => 'Set as Default',
        'icon' => 'fas fa-star',
        'updateModelField' => 'is_default',
        'fieldValue' => true,
        'confirm' => 'Set selected schedule as the default?',
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
        'field' => 'frequency',
        'type' => 'select',
        'options' => [
          '0' => 'All',
          '1' => 'weekly',
          '2' => 'biweekly',
          '3' => 'semi_monthly',
          '4' => 'monthly',
          '5' => 'quarterly',
          '6' => 'yearly',
        ],
        'label' => 'Frequency',
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
        'field' => 'is_default',
        'type' => 'select',
        'options' => [
          '0' => 'All',
          '1' => 'Default',
          '2' => 'Not Default',
        ],
        'label' => 'Default Schedule',
      ],
      '3' => [
        'field' => 'country_code',
        'type' => 'select',
        'optionsFrom' => 'countries',
        'label' => 'Country',
      ],
      '4' => [
        'field' => 'currency_code',
        'type' => 'select',
        'optionsFrom' => 'currencies',
        'label' => 'Currency',
      ],
      '5' => [
        'field' => 'next_pay_date',
        'type' => 'date_range',
        'label' => 'Next Pay Date Range',
      ],
    ],
  ],
  'fieldGroups' => [
    'basic_info' => [
      'title' => 'Schedule Information',
      'groupType' => 'payroll',
      'icon' => 'fas fa-info-circle',
      'fields' => [
        '0' => 'name',
        '1' => 'code',
        '2' => 'frequency',
        '3' => 'description',
      ],
    ],
    'dates' => [
      'title' => 'Dates & Timing',
      'groupType' => 'payroll',
      'icon' => 'fas fa-calendar-alt',
      'fields' => [
        '0' => 'first_period_start_date',
        '1' => 'next_pay_date',
        '2' => 'payment_delay_days',
      ],
    ],
    'jurisdiction' => [
      'title' => 'Jurisdiction & Defaults',
      'groupType' => 'payroll',
      'icon' => 'fas fa-globe',
      'fields' => [
        '0' => 'country_code',
        '1' => 'state_code',
        '2' => 'currency_code',
        '3' => 'timezone',
      ],
    ],
    'status' => [
      'title' => 'Status',
      'groupType' => 'payroll',
      'icon' => 'fas fa-toggle-on',
      'fields' => [
        '0' => 'is_active',
        '1' => 'is_default',
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
      'title' => 'Copy Schedule',
      'icon' => 'fas fa-copy',
      'route' => 'pay-schedules.copy',
      'params' => [
        'id' => '{id}',
      ],
      'confirm' => 'Create a copy of this pay schedule?',
      'requiredRole' => [
        '0' => 'hr_admin',
        '1' => 'payroll_officer',
      ],
    ],
    '1' => [
      'title' => 'View Payroll Runs',
      'icon' => 'fas fa-file-invoice-dollar',
      'route' => 'payroll-runs.index',
      'params' => [
        'filters[pay_schedule_id]' => '{id}',
      ],
      'newTab' => true,
    ],
    '2' => [
      'title' => 'View Assigned Employees',
      'icon' => 'fas fa-users',
      'route' => 'employee-payroll-profiles.index',
      'params' => [
        'filters[pay_schedule_id]' => '{id}',
      ],
      'newTab' => true,
    ],
    '3' => [
      'title' => 'Recalculate Next Pay Date',
      'icon' => 'fas fa-sync-alt',
      'dispatchStandardEvent' => true,
      'eventClass' => 'App\Modules\System\Events\PayScheduleRecalculated',
      'params' => [
        'schedule_id' => '{id}',
      ],
      'confirm' => 'Recalculate next pay date based on frequency and first period start?',
      'requiredRole' => [
        '0' => 'admin',
        '1' => 'payroll_officer',
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
        '1' => 'frequency',
      ],
      'contentFields' => [
        '0' => 'next_pay_date',
        '1' => 'country_code',
      ],
      'badgeField' => 'is_active',
      'badgeColors' => [
        'true' => 'success',
        'false' => 'secondary',
      ],
      'ribbonField' => 'is_default',
      'ribbonText' => 'Default',
      'ribbonColor' => 'warning',
    ],
    'list' => [
      'titleFields' => [
        '0' => 'name',
      ],
      'subtitleFields' => [
        '0' => 'code',
        '1' => 'frequency',
      ],
      'contentFields' => [
        '0' => 'next_pay_date',
        '1' => 'country_code',
        '2' => 'currency_code',
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
        '1' => 'frequency',
      ],
      'tabs' => [
        '0' => [
          'title' => 'Overview',
          'icon' => 'fas fa-info-circle',
          'fields' => [
            '0' => 'name',
            '1' => 'code',
            '2' => 'frequency',
            '3' => 'description',
            '4' => 'is_active',
            '5' => 'is_default',
          ],
        ],
        '1' => [
          'title' => 'Dates & Timing',
          'icon' => 'fas fa-calendar-alt',
          'fields' => [
            '0' => 'first_period_start_date',
            '1' => 'next_pay_date',
            '2' => 'payment_delay_days',
          ],
        ],
        '2' => [
          'title' => 'Jurisdiction',
          'icon' => 'fas fa-globe',
          'fields' => [
            '0' => 'country_code',
            '1' => 'state_code',
            '2' => 'currency_code',
            '3' => 'timezone',
          ],
        ],
        '3' => [
          'title' => 'Payroll Runs',
          'icon' => 'fas fa-file-invoice-dollar',
          'relation' => 'payrollRuns',
          'relationLimit' => 20,
        ],
        '4' => [
          'title' => 'Assigned Employees',
          'icon' => 'fas fa-users',
          'relation' => 'employeeProfiles',
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
    'payrollRuns' => [
      'type' => 'hasMany',
      'model' => 'App\Modules\Hr\Models\PayrollRun',
      'foreignKey' => 'pay_schedule_id',
      'localKey' => '',
    ],
    'employeeProfiles' => [
      'type' => 'hasMany',
      'model' => 'App\Modules\Hr\Models\EmployeePayrollProfile',
      'foreignKey' => 'pay_schedule_id',
      'localKey' => '',
    ],
  ],
  'report' => [],
];
