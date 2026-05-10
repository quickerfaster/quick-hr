<?php

return [
  'model' => 'App\Modules\Hr\Models\PayslipItem',
  'fieldDefinitions' => [
    'payslip_id' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Payslip',
      'validation' => 'required|exists:payroll_payslips,id',
      'relationship' => [
        'model' => 'App\Modules\Hr\Models\PayrollPayslip',
        'type' => 'belongsTo',
        'display_field' => 'payslip_number',
        'dynamic_property' => 'payslip',
        'foreign_key' => 'payslip_id',
        'inlineAdd' => false,
      ],
      'options' => [
        'model' => 'App\Modules\Hr\Models\PayrollPayslip',
        'column' => 'payslip_number',
        'hintField' => '',
      ],
    ],
    'type' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Item Type',
      'validation' => 'required|in:earning,deduction,tax,reimbursement',
      'options' => [
        'earning' => 'Earning',
        'deduction' => 'Deduction',
        'tax' => 'Tax',
        'reimbursement' => 'Reimbursement',
      ],
      'filterable' => true,
    ],
    'label' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Description',
      'validation' => 'required|string|max:255',
      'searchable' => true,
    ],
    'amount' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'number',
      'label' => 'Amount',
      'validation' => 'required|numeric',
    ],
    'policy_id' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Linked Policy',
      'validation' => 'nullable|exists:payroll_policies,id',
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
    'adjustment_id' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Linked Adjustment',
      'validation' => 'nullable|exists:payroll_run_adjustments,id',
      'relationship' => [
        'model' => 'App\Modules\Hr\Models\PayrollRunAdjustment',
        'type' => 'belongsTo',
        'display_field' => 'label',
        'dynamic_property' => 'adjustment',
        'foreign_key' => 'adjustment_id',
        'inlineAdd' => false,
      ],
      'options' => [
        'model' => 'App\Modules\Hr\Models\PayrollRunAdjustment',
        'column' => 'label',
        'hintField' => '',
      ],
    ],
    'employee_adjustment_profile_id' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Recurring Adjustment Profile',
      'validation' => 'nullable|exists:employee_adjustment_profiles,id',
      'relationship' => [
        'model' => 'App\Modules\Hr\Models\EmployeeAdjustmentProfile',
        'type' => 'belongsTo',
        'display_field' => 'label',
        'dynamic_property' => 'employeeAdjustmentProfile',
        'foreign_key' => 'employee_adjustment_profile_id',
        'inlineAdd' => false,
      ],
      'options' => [
        'model' => 'App\Modules\Hr\Models\EmployeeAdjustmentProfile',
        'column' => 'label',
        'hintField' => '',
      ],
    ],
    'calculation_metadata' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'json',
      'label' => 'Calculation Metadata',
      'validation' => 'nullable|json',
    ],
  ],
  'detailComponent' => '',
  'hiddenFields' => [
    'onTable' => [
      '0' => 'policy_id',
      '1' => 'adjustment_id',
      '2' => 'employee_adjustment_profile_id',
      '3' => 'calculation_metadata',
    ],
    'onNewForm' => [
      '0' => 'calculation_metadata',
    ],
    'onEditForm' => [
      '0' => 'calculation_metadata',
    ],
    'onQuery' => [],
  ],
  'simpleActions' => [
    '0' => 'show',
  ],
  'isTransaction' => false,
  'crudType' => 'modals',
  'includeControllers' => false,
  'tableDefaultFields' => [],
  'addRoutes' => false,
  'dispatchEvents' => false,
  'controls' => [
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
        'field' => 'type',
        'type' => 'select',
        'options' => [
          '0' => 'All',
          '1' => 'earning',
          '2' => 'deduction',
          '3' => 'tax',
          '4' => 'reimbursement',
        ],
        'label' => 'Item Type',
      ],
      '1' => [
        'field' => 'amount',
        'type' => 'number_range',
        'label' => 'Amount Range',
      ],
      '2' => [
        'field' => 'created_at',
        'type' => 'date_range',
        'label' => 'Created Date',
      ],
    ],
  ],
  'fieldGroups' => [
    'basic_info' => [
      'title' => 'Item Information',
      'groupType' => 'payroll',
      'icon' => 'fas fa-info-circle',
      'fields' => [
        '0' => 'payslip_id',
        '1' => 'type',
        '2' => 'label',
        '3' => 'amount',
      ],
    ],
    'traceability' => [
      'title' => 'Traceability',
      'groupType' => 'payroll',
      'icon' => 'fas fa-link',
      'fields' => [
        '0' => 'policy_id',
        '1' => 'adjustment_id',
        '2' => 'employee_adjustment_profile_id',
      ],
    ],
    'metadata' => [
      'title' => 'Calculation Metadata',
      'groupType' => 'payroll',
      'icon' => 'fas fa-cog',
      'fields' => [
        '0' => 'calculation_metadata',
      ],
    ],
  ],
  'moreActions' => [],
  'switchViews' => [
    'default' => 'list',
    'detail' => [
      'layout' => 'tab',
      'detailType' => 'record',
      'titleFields' => [
        '0' => 'label',
        '1' => 'type',
      ],
      'subtitleFields' => [
        '0' => 'amount',
      ],
      'tabs' => [
        '0' => [
          'title' => 'Overview',
          'icon' => 'fas fa-info-circle',
          'fields' => [
            '0' => 'payslip_id',
            '1' => 'type',
            '2' => 'label',
            '3' => 'amount',
          ],
        ],
        '1' => [
          'title' => 'Traceability',
          'icon' => 'fas fa-link',
          'fields' => [
            '0' => 'policy_id',
            '1' => 'adjustment_id',
            '2' => 'employee_adjustment_profile_id',
          ],
        ],
      ],
    ],
  ],
  'relations' => [
    'payslip' => [
      'type' => 'belongsTo',
      'model' => 'App\Modules\Hr\Models\PayrollPayslip',
      'foreignKey' => 'payslip_id',
      'localKey' => '',
    ],
    'policy' => [
      'type' => 'belongsTo',
      'model' => 'App\Modules\Hr\Models\PayrollPolicy',
      'foreignKey' => 'policy_id',
      'localKey' => '',
    ],
    'adjustment' => [
      'type' => 'belongsTo',
      'model' => 'App\Modules\Hr\Models\PayrollRunAdjustment',
      'foreignKey' => 'adjustment_id',
      'localKey' => '',
    ],
    'employeeAdjustmentProfile' => [
      'type' => 'belongsTo',
      'model' => 'App\Modules\Hr\Models\EmployeeAdjustmentProfile',
      'foreignKey' => 'employee_adjustment_profile_id',
      'localKey' => '',
    ],
  ],
  'report' => [],
];
