<?php

return [
  'model' => 'App\Modules\Hr\Models\PaySchedule',
  'fieldDefinitions' => [
    'name' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Schedule Name',
      'validation' => 'required|string|max:100',
      'reactivity' => false,
    ],
    'frequency' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Pay Frequency',
      'validation' => 'required',
      'options' => [
        'Weekly' => 'Weekly',
        'Bi-weekly' => 'Bi-weekly',
        'Semi-monthly' => 'Semi-monthly',
        'Monthly' => 'Monthly',
      ],
      'reactivity' => false,
    ],
    'next_pay_date' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'datepicker',
      'label' => 'Next Pay Date',
      'validation' => 'required|date',
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
  ],
  'detailComponent' => '',
  'hiddenFields' => [
    'onTable' => [],
    'onNewForm' => [],
    'onEditForm' => [],
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
  'controls' => 'all',
  'fieldGroups' => [
    'schedule_details' => [
      'title' => 'Schedule Details',
      'groupType' => 'payroll',
      'fields' => [
        '0' => 'name',
        '1' => 'frequency',
        '2' => 'next_pay_date',
        '3' => 'is_active',
      ],
    ],
  ],
  'moreActions' => [],
  'switchViews' => [],
  'relations' => [],
  'report' => [],
];
