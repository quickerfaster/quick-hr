<?php

return [
  'model' => 'App\Modules\Admin\Models\Role',
  'fieldDefinitions' => [
    'name' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Role Name',
      'validation' => 'required|string|max:255',
      'reactivity' => false,
      'wizard' => [
        'role_setup' => true,
      ],
    ],
    'description' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'textarea',
      'label' => 'Description',
      'validation' => 'nullable|string|max:1000',
      'reactivity' => false,
    ],
    'editable' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Editable',
      'validation' => 'required|in:Yes,No',
      'options' => [
        'Yes' => 'Yes',
        'No' => 'No',
      ],
      'reactivity' => false,
    ],
    'guard_name' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Guard',
      'validation' => 'required|string|in:web,api',
      'options' => [
        'web' => 'Web',
        'api' => 'API',
      ],
      'reactivity' => false,
    ],
  ],
  'detailComponent' => '',
  'hiddenFields' => [
    'onTable' => [
      '0' => 'permissions',
    ],
    'onNewForm' => [
      '0' => 'permissions',
    ],
    'onEditForm' => [
      '0' => 'permissions',
    ],
    'onQuery' => [
      '0' => 'permissions',
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
        'label' => 'Add Role',
        'type' => 'quick_add',
        'icon' => 'fas fa-plus',
        'primary' => true,
      ],
      '1' => [
        'label' => 'Create Role with Permissions',
        'type' => 'wizard',
        'wizard' => 'role_permission_setup',
        'icon' => 'fas fa-shield-alt',
      ],
    ],
    'files' => [
      'export' => [
        '0' => 'csv',
        '1' => 'json',
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
    'basic_info' => [
      'title' => 'Role Details',
      'groupType' => 'auth',
      'fields' => [
        '0' => 'name',
        '1' => 'description',
      ],
    ],
    'access_control' => [
      'title' => 'Admin Settings',
      'groupType' => 'auth',
      'fields' => [
        '0' => 'editable',
        '1' => 'guard_name',
      ],
    ],
  ],
  'moreActions' => [],
  'switchViews' => [],
  'relations' => [],
  'report' => [],
];
