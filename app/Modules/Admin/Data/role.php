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
      'filterable' => true,
      'searchable' => true,
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
      'filterable' => true,
    ],
  ],
  'detailComponent' => '',
  'hiddenFields' => [
    'onTable' => [
      '0' => 'permissions',
      '1' => 'created_at',
      '2' => 'updated_at',
      '3' => 'deleted_at',
    ],
    'onNewForm' => [
      '0' => 'permissions',
      '1' => 'created_at',
      '2' => 'updated_at',
      '3' => 'deleted_at',
    ],
    'onEditForm' => [
      '0' => 'permissions',
      '1' => 'deleted_at',
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
    '2' => 'editable',
  ],
  'addRoutes' => false,
  'dispatchEvents' => false,
  'controls' => [
    'addButton' => true,
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
      ],
      '1' => [
        'field' => 'editable',
        'type' => 'select',
        'options' => [
          '0' => 'All',
          '1' => 'Yes',
          '2' => 'No',
        ],
        'label' => 'Editable',
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
      'title' => 'Role Details',
      'groupType' => 'auth',
      'icon' => 'fas fa-info-circle',
      'fields' => [
        '0' => 'name',
        '1' => 'description',
      ],
    ],
    'access_control' => [
      'title' => 'Access Control',
      'groupType' => 'auth',
      'icon' => 'fas fa-lock',
      'fields' => [
        '0' => 'guard_name',
        '1' => 'editable',
      ],
    ],
  ],
  'moreActions' => [
    '0' => [
      'title' => 'Restore Role',
      'icon' => 'fas fa-trash-restore',
      'action' => 'restore',
      'confirm' => 'Restore this archived role?',
      'requiredPermission' => 'restore_role',
      'condition' => ['trashed' => [true]],
    ],
    '1' => [
      'title' => 'Permanently Delete',
      'icon' => 'fas fa-skull-crossbones',
      'action' => 'forceDelete',
      'confirm' => 'This action cannot be undone. Permanently delete this role?',
      'requiredPermission' => 'force_delete_role',
      'condition' => ['trashed' => [true]],
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
      'badgeField' => 'editable',
      'badgeColors' => [
        'Yes' => 'success',
        'No' => 'secondary',
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
