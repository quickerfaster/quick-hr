<?php

return [
  'model' => 'App\Modules\Admin\Models\Permission',
  'fieldDefinitions' => [
    'name' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Permission Name',
      'validation' => 'required|string|max:255',
      'filterable' => true,
      'searchable' => true,
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
      'searchable' => true,
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
      'filterable' => true,
    ],
  ],
  'detailComponent' => '',
  'hiddenFields' => [
    'onTable' => [
      '0' => 'created_at',
      '1' => 'updated_at',
      '2' => 'deleted_at',
    ],
    'onNewForm' => [
      '0' => 'created_at',
      '1' => 'updated_at',
      '2' => 'deleted_at',
    ],
    'onEditForm' => [
      '0' => 'deleted_at',
    ],
    'onQuery' => [
      '0' => 'deleted_at',
    ],
  ],
  'simpleActions' => [
    '0' => 'show',
    '1' => 'edit',
    '2' => 'delete',
  ],
  'isTransaction' => false,
  'crudType' => 'drawers',
  'includeControllers' => false,
  'tableDefaultFields' => [
    '0' => 'name',
    '1' => 'guard_name',
  ],
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
    'filterColumns' => true,
    'filters' => [
      '0' => [
        'field' => 'guard_name',
        'type' => 'select',
        'options' => [
          '0' => 'All',
          '1' => 'web',
          '2' => 'api',
        ],
        'label' => 'Guard',
        'default' => 'web',
      ],
    ],
    'softDelete' => true,
    'restore' => true,
    'forceDelete' => true,
    'trashView' => true,
    'bulkActions' => [
      'export' => [
        '0' => 'csv',
        '1' => 'json',
      ],
      'delete' => true,
      'restore' => true,
      'forceDelete' => true,
    ],
  ],
  'fieldGroups' => [
    'basic_info' => [
      'title' => 'Permission Details',
      'groupType' => 'auth',
      'icon' => 'fas fa-info-circle',
      'fields' => [
        '0' => 'name',
        '1' => 'description',
        '2' => 'guard_name',
      ],
    ],
  ],
  'moreActions' => [
    '0' => [
      'title' => 'Restore Permission',
      'icon' => 'fas fa-trash-restore',
      'action' => 'restore',
      'confirm' => 'Restore this archived permission?',
      'requiredPermission' => 'restore_permission',
      'condition' => 'trashed',
    ],
    '1' => [
      'title' => 'Permanently Delete',
      'icon' => 'fas fa-skull-crossbones',
      'action' => 'forceDelete',
      'confirm' => 'This action cannot be undone. Permanently delete this permission?',
      'requiredPermission' => 'force_delete_permission',
      'condition' => 'trashed',
    ],
  ],
  'switchViews' => [
    'default' => 'list',
    'list' => [
      'enabled' => true,
      'titleFields' => [
        '0' => 'name',
      ],
      'subtitleFields' => [
        '0' => 'guard_name',
      ],
      'contentFields' => [
        '0' => 'description',
      ],
    ],
    'table' => [
      'enabled' => true,
    ],
    'card' => [
      'enabled' => false,
    ],
  ],
  'relations' => [],
  'report' => [],
];
