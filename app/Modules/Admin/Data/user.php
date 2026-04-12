<?php

return [
  'model' => 'App\Modules\Admin\Models\User',
  'fieldDefinitions' => [
    'name' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Full Name',
      'validation' => 'required|string|max:255',
      'reactivity' => false,
      'wizard' => [
        'user_onboarding' => true,
      ],
    ],
    'email' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Email Address',
      'validation' => 'required|email|max:255|unique:users,email',
      'reactivity' => false,
      'wizard' => [
        'user_onboarding' => true,
      ],
    ],
    'email_verified_at' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'datetimepicker',
      'label' => 'Email Verified At',
      'validation' => 'nullable|date',
      'reactivity' => false,
    ],
    'password' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'password',
      'label' => 'Password',
      'validation' => 'required|string|min:8|confirmed',
      'reactivity' => false,
      'wizard' => [
        'user_onboarding' => true,
      ],
    ],
    'password_confirmation' => [
      'display' => 'inline',
      'fillable' => false,
      'field_type' => 'password',
      'label' => 'Confirm Password',
      'validation' => 'required_with:password|same:password',
      'reactivity' => false,
    ],
    'remember_token' => [
      'display' => 'inline',
      'fillable' => false,
      'field_type' => 'string',
      'label' => 'Remember Token',
      'validation' => 'nullable|string|max:255',
      'reactivity' => false,
    ],
    'status' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Status',
      'validation' => 'required',
      'options' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'invited' => 'Invited',
      ],
      'reactivity' => false,
    ],
  ],
  'detailComponent' => '',
  'hiddenFields' => [
    'onTable' => [
      '0' => 'password_confirmation',
      '1' => 'password',
      '2' => 'remember_token',
      '3' => 'email_verified_at',
    ],
    'onNewForm' => [
      '0' => 'email_verified_at',
      '1' => 'remember_token',
      '2' => 'status',
    ],
    'onEditForm' => [
      '0' => 'remember_token',
      '1' => 'email_verified_at',
    ],
    'onQuery' => [
      '0' => 'password_confirmation',
    ],
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
  'controls' => [
    'addButton' => [
      '0' => [
        'label' => 'Invite User',
        'type' => 'quick_add',
        'icon' => 'fas fa-user-plus',
        'primary' => true,
      ],
      '1' => [
        'label' => 'Bulk Invite',
        'type' => 'bulk_invite',
        'icon' => 'fas fa-envelope',
      ],
    ],
    'files' => [
      'export' => [
        '0' => 'csv',
        '1' => 'xls',
      ],
      'print' => false,
    ],
    'perPage' => [
      '0' => 10,
      '1' => 25,
      '2' => 50,
      '3' => 100,
    ],
    'search' => true,
    'showHideColumns' => true,
  ],
  'fieldGroups' => [
    'identity' => [
      'title' => 'Identity',
      'groupType' => 'admin',
      'fields' => [
        '0' => 'name',
        '1' => 'email',
      ],
    ],
    'authentication' => [
      'title' => 'Authentication',
      'groupType' => 'admin',
      'fields' => [
        '0' => 'password',
        '1' => 'password_confirmation',
      ],
    ],
    'system' => [
      'title' => 'System Info',
      'groupType' => 'admin',
      'fields' => [
        '0' => 'status',
      ],
    ],
  ],
  'moreActions' => [],
  'switchViews' => [],
  'relations' => [
    'roles' => [
      'type' => 'morphToMany',
      'model' => 'Spatie\Permission\Models\Role',
      'foreignKey' => 'model_id',
      'localKey' => '',
    ],
    'employee' => [
      'type' => 'hasOne',
      'model' => 'App\Modules\Hr\Models\Employee',
      'foreignKey' => 'user_id',
      'localKey' => '',
    ],
  ],
  'report' => [],
];
