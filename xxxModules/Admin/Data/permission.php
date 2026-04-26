<?php

return [
  'model' => 'App\Modules\Admin\Models\Permission',
  'fieldDefinitions' => [
    'name' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Name',
      'validation' => 'required|string|max:255',
      'reactivity' => false,
      'wizard' => [
        'permission_setup' => true,
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
  'controls' => [
    'addButton' => [
      '0' => [
        'label' => 'Add Permission',
        'type' => 'quick_add',
        'icon' => 'fas fa-plus',
        'primary' => true,
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
      'title' => 'Permission Details',
      'groupType' => 'auth',
      'fields' => [
        '0' => 'name',
        '1' => 'description',
        '2' => 'guard_name',
      ],
    ],
  ],
  'moreActions' => [],
  'switchViews' => [],
  'relations' => [],
  'report' => [],
];
