<?php

return [
  'model' => 'App\Modules\Admin\Models\JobTitle',
  'fieldDefinitions' => [
    'title' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Job Title',
      'validation' => 'required|string|max:255|unique:job_titles,title',
      'filterable' => true,
      'searchable' => true,
    ],
    'description' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'textarea',
      'label' => 'Description',
      'validation' => 'nullable|string',
      'searchable' => true,
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
      '0' => 'updated_at',
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
    '0' => 'title',
    '1' => 'description',
  ],
  'addRoutes' => false,
  'dispatchEvents' => false,
  'controls' => [
    'addButton' => true,
    'search' => true,
    'perPage' => [
      '0' => 10,
      '1' => 25,
      '2' => 50,
      '3' => 100,
    ],
    'files' => [
      'export' => [
        '0' => 'xls',
        '1' => 'csv',
        '2' => 'pdf',
      ],
      'print' => true,
    ],
    'showHideColumns' => true,
    'filterColumns' => true,
    'filters' => [
      '0' => [
        'field' => 'title',
        'type' => 'text',
        'label' => 'Job Title',
      ],
    ],
    'softDelete' => true,
    'restore' => true,
    'forceDelete' => true,
    'trashView' => true,
    'bulkActions' => [
      'export' => [
        '0' => 'xls',
        '1' => 'csv',
        '2' => 'pdf',
      ],
      'delete' => true,
      'restore' => true,
      'forceDelete' => true,
    ],
  ],
  'fieldGroups' => [
    '0' => [
      'title' => 'Job Title Information',
      'groupType' => 'hr',
      'icon' => 'fas fa-info-circle',
      'fields' => [
        '0' => 'title',
        '1' => 'description',
      ],
    ],
  ],
  'moreActions' => [
    '0' => [
      'title' => 'Restore',
      'icon' => 'fas fa-trash-restore',
      'action' => 'restore',
      'confirm' => 'Restore this job title?',
      'requiredPermission' => 'restore_job_title',
      'condition' => 'trashed',
    ],
    '1' => [
      'title' => 'Permanently Delete',
      'icon' => 'fas fa-skull-crossbones',
      'action' => 'forceDelete',
      'confirm' => 'This action cannot be undone. Permanently delete this job title?',
      'requiredPermission' => 'force_delete_job_title',
      'condition' => 'trashed',
    ],
  ],
  'switchViews' => [
    'default' => 'list',
    'table' => [
      'enabled' => true,
    ],
    'list' => [
      'enabled' => true,
      'titleFields' => [
        '0' => 'title',
      ],
      'subtitleFields' => [],
      'contentFields' => [
        '0' => 'description',
      ],
    ],
    'card' => [
      'enabled' => true,
      'titleFields' => [
        '0' => 'title',
      ],
      'contentFields' => [
        '0' => 'description',
      ],
    ],
  ],
  'relations' => [],
  'report' => [],
];
