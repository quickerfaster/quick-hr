<?php

return [
  'model' => 'App\Modules\Hr\Models\PayrollRun',
  'fieldDefinitions' => [
    'pay_schedule_id' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Pay Schedule',
      'validation' => 'required|exists:pay_schedules,id',
      'reactivity' => false,
      'relationship' => [
        'model' => 'App\Modules\Hr\Models\PaySchedule',
        'type' => 'belongsTo',
        'display_field' => 'name',
        'dynamic_property' => 'paySchedule',
        'foreign_key' => 'pay_schedule_id',
        'inlineAdd' => false,
      ],
      'options' => [
        'model' => 'App\Modules\Hr\Models\PaySchedule',
        'column' => 'name',
        'hintField' => '',
      ],
    ],
    'period_start' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'datepicker',
      'label' => 'Pay Period Start',
      'validation' => 'required|date',
      'reactivity' => false,
    ],
    'period_end' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'datepicker',
      'label' => 'Pay Period End',
      'validation' => 'required|date|after:period_start',
      'reactivity' => false,
    ],
    'status' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Status',
      'validation' => 'required',
      'options' => [
        'Draft' => 'Draft',
        'Approved' => 'Approved',
        'Paid' => 'Paid',
        'Cancelled' => 'Cancelled',
      ],
      'reactivity' => false,
    ],
    'processed_by' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Processed By',
      'validation' => 'nullable|string|max:255',
      'reactivity' => false,
    ],
    'processed_at' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'datetimepicker',
      'label' => 'Processed At',
      'validation' => 'nullable|date',
      'reactivity' => false,
    ],
    'notes' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'textarea',
      'label' => 'Notes',
      'validation' => 'nullable|string',
      'reactivity' => false,
    ],
  ],
  'detailComponent' => '',
  'hiddenFields' => [
    'onTable' => [],
    'onNewForm' => [
      '0' => 'processed_by',
      '1' => 'processed_at',
    ],
    'onEditForm' => [
      '0' => 'processed_by',
      '1' => 'processed_at',
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
    'addButton' => [
      '0' => [
        'label' => 'New Pay Run',
        'type' => 'quick_add',
        'icon' => 'fas fa-plus',
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
    ],
    'perPage' => [
      '0' => 5,
      '1' => 10,
      '2' => 25,
      '3' => 50,
    ],
    'search' => true,
    'showHideColumns' => true,
  ],
  'fieldGroups' => [
    'run_details' => [
      'title' => 'Pay Run Details',
      'groupType' => 'payroll',
      'fields' => [
        '0' => 'pay_schedule_id',
        '1' => 'period_start',
        '2' => 'period_end',
        '3' => 'status',
        '4' => 'notes',
      ],
    ],
    'processing_info' => [
      'title' => 'Processing',
      'groupType' => 'payroll',
      'fields' => [
        '0' => 'processed_by',
        '1' => 'processed_at',
      ],
    ],
  ],
  'moreActions' => [
    '0' => [
      'title' => 'Preview Payroll',
      'icon' => 'fas fa-eye',
      'dispatchBrowserEvent' => true,
      'eventName' => 'openPayrollPreviewModalEvent',
      'params' => [
        'payroll_run_id' => '{id}',
      ],
      'condition' => [
        '0' => [
          'status' => 'draft',
        ],
      ],
    ],
    '1' => [
      'title' => 'Approve Payroll',
      'icon' => 'fas fa-check-circle',
      'updateModelField' => true,
      'fieldName' => 'status',
      'fieldValue' => 'approved',
      'actionName' => 'approve_payroll',
      'confirm' => 'Approve this payroll run? This will lock all data.',
      'condition' => [
        '0' => [
          'status' => 'draft',
        ],
      ],
    ],
    '2' => [
      'title' => 'Cancel Run',
      'icon' => 'fas fa-ban',
      'updateModelField' => true,
      'fieldName' => 'status',
      'fieldValue' => 'cancelled',
      'actionName' => 'cancel_payroll',
      'confirm' => 'Cancel this payroll run? This cannot be undone.',
      'condition' => [
        '0' => [
          'status' => [
            '0' => 'draft',
            '1' => 'approved',
          ],
        ],
      ],
    ],
    '3' => [
      'title' => 'Mark as Paid',
      'icon' => 'fas fa-money-check',
      'updateModelField' => true,
      'fieldName' => 'status',
      'fieldValue' => 'paid',
      'actionName' => 'mark_paid',
      'confirm' => 'Confirm payment has been processed externally?',
      'condition' => [
        '0' => [
          'status' => 'approved',
        ],
      ],
    ],
    '4' => [
      'title' => 'View Report',
      'icon' => 'fas fa-file-alt',
      'route' => 'payroll.reports.show',
      'params' => [
        'payrollRun' => '{id}',
      ],
      'condition' => [
        '0' => [
          'status' => [
            '0' => 'approved',
            '1' => 'paid',
          ],
        ],
      ],
    ],
  ],
  'switchViews' => [],
  'relations' => [
    'paySchedule' => [
      'type' => 'belongsTo',
      'model' => 'App\Modules\Hr\Models\PaySchedule',
      'foreignKey' => 'pay_schedule_id',
      'localKey' => '',
    ],
    'payslips' => [
      'type' => 'hasMany',
      'model' => 'App\Modules\Hr\Models\PayrollPayslip',
      'foreignKey' => 'payroll_run_id',
      'localKey' => '',
    ],
  ],
  'report' => [],
];
