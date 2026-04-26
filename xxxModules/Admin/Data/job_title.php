<?php

return [
  'model' => 'App\Modules\Admin\Models\JobTitle',
  'fieldDefinitions' => [
    'title' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Title',
      'validation' => 'required|unique:job_titles,title',
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
    'addButton' => true,
    'search' => true,
    'perPage' => [
      '0' => 5,
      '1' => 10,
      '2' => 25,
      '3' => 50,
      '4' => 100,
      '5' => 500,
    ],
    'files' => [
      'export' => [
        '0' => 'xls',
        '1' => 'csv',
        '2' => 'pdf',
      ],
      'print' => true,
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
      'confirm' => 'Restore this record?',
      'requiredPermission' => 'restore_employee',
      'condition' => 'trashed',
    ],
    '1' => [
      'title' => 'Permanently Delete',
      'icon' => 'fas fa-skull-crossbones',
      'action' => 'forceDelete',
      'confirm' => 'This action cannot be undone. Permanently delete?',
      'requiredPermission' => 'force_delete_employee',
      'condition' => 'trashed',
    ],
  ],
  'switchViews' => [],
  'relations' => [],
  'report' => [],
];
