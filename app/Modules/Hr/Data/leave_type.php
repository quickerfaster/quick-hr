<?php

return [
  'model' => 'App\Modules\Hr\Models\LeaveType',
  'fieldDefinitions' => [
    'name' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Leave Type Name',
      'validation' => 'required|string|max:255',
      'reactivity' => false,
    ],
    'code' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Short Code',
      'validation' => 'required|string|max:10|unique:leave_types,code',
      'reactivity' => false,
    ],
    'deducts_from_balance' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'radio',
      'label' => 'Deducts From Balance',
      'validation' => 'required',
      'options' => [
        'Yes' => 'Yes',
        'No' => 'No',
      ],
      'reactivity' => false,
    ],
    'requires_approval' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'radio',
      'label' => 'Requires Approval',
      'validation' => 'required',
      'options' => [
        'Yes' => 'Yes',
        'No' => 'No',
      ],
      'reactivity' => false,
    ],
    'max_days_per_request' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'number',
      'label' => 'Max Days Per Request',
      'validation' => 'nullable|integer|min:1',
      'reactivity' => false,
    ],
    'is_active' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'radio',
      'label' => 'Is Active',
      'validation' => 'required',
      'options' => [
        'Active' => 'Active',
        'Inactive' => 'Inactive',
      ],
      'reactivity' => false,
    ],
    'description' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'textarea',
      'label' => 'Description',
      'validation' => 'nullable|string',
      'reactivity' => false,
    ],
  ],
  'detailComponent' => '',
  'hiddenFields' => [],
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
    'addButton' => false,
  ],
  'fieldGroups' => [
    'basic_info' => [
      'title' => 'Basic Information',
      'groupType' => 'hr',
      'fields' => [
        '0' => 'name',
        '1' => 'code',
        '2' => 'color',
        '3' => 'description',
      ],
    ],
    'rules' => [
      'title' => 'Leave Rules',
      'groupType' => 'hr',
      'fields' => [
        '0' => 'deducts_from_balance',
        '1' => 'requires_approval',
        '2' => 'max_days_per_request',
        '3' => 'is_active',
      ],
    ],
  ],
  'moreActions' => [],
  'switchViews' => [],
  'relations' => [
    'leaveBalances' => [
      'type' => 'hasMany',
      'model' => 'App\Modules\Hr\Models\LeaveBalance',
      'foreignKey' => 'leave_type_id',
      'localKey' => '',
    ],
  ],
  'report' => [],
];
