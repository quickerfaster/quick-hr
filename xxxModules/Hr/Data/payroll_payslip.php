<?php

return [
  'model' => 'App\Modules\Hr\Models\PayrollPayslip',
  'fieldDefinitions' => [
    'payslip_number' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Payslip Number',
      'validation' => 'required|string|max:50|unique:payroll_payslips,payslip_number',
      'reactivity' => false,
    ],
    'payroll_run_id' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Payroll Run',
      'validation' => 'required|exists:payroll_runs,id',
      'reactivity' => false,
      'relationship' => [
        'model' => 'App\Modules\Hr\Models\PayrollRun',
        'type' => 'belongsTo',
        'display_field' => 'period_end',
        'dynamic_property' => 'payrollRun',
        'foreign_key' => 'payroll_run_id',
        'inlineAdd' => false,
      ],
      'options' => [
        'model' => 'App\Modules\Hr\Models\PayrollRun',
        'column' => 'period_end',
        'hintField' => '',
      ],
    ],
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
        'hintField' => 'first_name',
      ],
    ],
    'base_salary' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'number',
      'label' => 'Base Salary (Period)',
      'validation' => 'required|numeric|min:0',
      'reactivity' => false,
    ],
    'gross_pay' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'number',
      'label' => 'Gross Pay',
      'validation' => 'required|numeric|min:0',
      'reactivity' => false,
    ],
    'total_deductions' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'number',
      'label' => 'Total Deductions',
      'validation' => 'required|numeric|min:0',
      'reactivity' => false,
    ],
    'net_pay' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'number',
      'label' => 'Net Pay',
      'validation' => 'required|numeric|min:0',
      'reactivity' => false,
    ],
    'paid_at' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'datetimepicker',
      'label' => 'Paid At',
      'validation' => 'nullable|date',
      'reactivity' => false,
    ],
  ],
  'detailComponent' => '',
  'hiddenFields' => [
    'onTable' => [
      '0' => 'payroll_run_id',
    ],
    'onNewForm' => [
      '0' => 'paid_at',
    ],
    'onEditForm' => [
      '0' => 'paid_at',
    ],
    'onQuery' => [],
  ],
  'simpleActions' => [
    '0' => 'show',
  ],
  'isTransaction' => false,
  'viewType' => 'modal',
  'includeControllers' => false,
  'addRoutes' => false,
  'dispatchEvents' => false,
  'controls' => [
    'files' => [
      'export' => [
        '0' => 'pdf',
      ],
      'print' => true,
    ],
    'perPage' => [
      '0' => 10,
      '1' => 25,
      '2' => 50,
    ],
    'search' => true,
  ],
  'fieldGroups' => [
    'employee_info' => [
      'title' => 'Employee',
      'groupType' => 'payroll',
      'fields' => [
        '0' => 'employee_id',
      ],
    ],
    'earnings' => [
      'title' => 'Earnings',
      'groupType' => 'payroll',
      'fields' => [
        '0' => 'base_salary',
        '1' => 'gross_pay',
      ],
    ],
    'deductions' => [
      'title' => 'Deductions',
      'groupType' => 'payroll',
      'fields' => [
        '0' => 'total_deductions',
      ],
    ],
    'net_pay' => [
      'title' => 'Net Pay',
      'groupType' => 'payroll',
      'fields' => [
        '0' => 'net_pay',
        '1' => 'paid_at',
      ],
    ],
  ],
  'moreActions' => [],
  'switchViews' => [
    'detail' => [
      'layout' => 'tab',
      'detailType' => 'record',
      'titleFields' => [
        '0' => 'employee.first_name',
        '1' => 'employee.last_name',
      ],
      'subtitleFields' => [
        '0' => 'net_pay',
        '1' => 'paid_at',
      ],
      'contentFields' => [
        '0' => 'base_salary',
        '1' => 'gross_pay',
        '2' => 'total_deductions',
      ],
    ],
  ],
  'relations' => [
    'payrollRun' => [
      'type' => 'belongsTo',
      'model' => 'App\Modules\Hr\Models\PayrollRun',
      'foreignKey' => 'payroll_run_id',
      'localKey' => '',
    ],
    'employee' => [
      'type' => 'belongsTo',
      'model' => 'App\Modules\Hr\Models\Employee',
      'foreignKey' => 'employee_id',
      'localKey' => '',
    ],
  ],
  'report' => [],
];
