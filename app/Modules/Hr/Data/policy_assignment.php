<?php

return [
  'model' => 'App\Modules\Hr\Models\PolicyAssignment',
  'fieldDefinitions' => [
    'attendance_policy_id' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Attendance Policy',
      'validation' => 'required|exists:attendance_policies,id',
      'relationship' => [
        'model' => 'App\Modules\Hr\Models\AttendancePolicy',
        'type' => 'belongsTo',
        'display_field' => 'name',
        'dynamic_property' => 'attendancePolicy',
        'foreign_key' => 'attendance_policy_id',
        'inlineAdd' => false,
      ],
      'options' => [
        'model' => 'App\Modules\Hr\Models\AttendancePolicy',
        'column' => 'name',
        'hintField' => '',
      ],
    ],
    'priority' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'number',
      'label' => 'Priority',
      'validation' => 'integer|min:0',
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
      ],
    ],
    'assignable_id' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'morph_to_select',
      'label' => 'Assign to Entity',
      'morph_relation' => 'assignable',
      'morph_map' => [
        'company' => 'App\Modules\Admin\Models\Company',
        'location' => 'App\Modules\Admin\Models\Location',
        'department' => 'App\Modules\Admin\Models\Department',
        'shift' => 'App\Modules\Admin\Models\Shift',
      ],
      'display_field' => 'name',
    ],
  ],
  'detailComponent' => '',
  'hiddenFields' => [
    'onTable' => [
      '0' => 'assignable_type',
    ],
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
  'crudType' => 'modals',
  'includeControllers' => false,
  'tableDefaultFields' => [],
  'addRoutes' => false,
  'dispatchEvents' => false,
  'controls' => 'all',
  'fieldGroups' => [
    'assignment' => [
      'title' => 'Policy Assignment',
      'groupType' => 'hr',
      'icon' => 'fas fa-link',
      'fields' => [
        '0' => 'assignable_id',
        '1' => 'attendance_policy_id',
        '2' => 'priority',
      ],
    ],
  ],
  'moreActions' => [],
  'switchViews' => [],
  'relations' => [
    'attendancePolicy' => [
      'type' => 'belongsTo',
      'model' => 'App\Modules\Hr\Models\AttendancePolicy',
      'foreignKey' => 'attendance_policy_id',
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
      ],
      'displayField' => 'name',
    ],
  ],
  'report' => [],
];
