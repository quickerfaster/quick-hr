<?php

return [
  'model' => 'App\Modules\Admin\Models\ActivityLog',
  'fieldDefinitions' => [
    'causer_type' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Causer Type',
      'reactivity' => false,
    ],
    'causer_id' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'number',
      'label' => 'Causer ID',
      'reactivity' => false,
    ],
    'subject_type' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Subject Type',
      'reactivity' => false,
    ],
    'subject_id' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'number',
      'label' => 'Subject ID',
      'reactivity' => false,
    ],
    'log_name' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Module',
      'validation' => 'nullable|string|max:255',
      'options' => [
        '0' => 'admin.activity_log',
        '1' => 'hr.employee',
        '2' => 'hr.attendance',
        '3' => 'hr.leave_request',
        '4' => 'hr.payroll_run',
      ],
      'reactivity' => false,
    ],
    'action' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Action',
      'validation' => 'required|string|max:255',
      'options' => [
        'created' => 'Created',
        'updated' => 'Updated',
        'deleted' => 'Deleted',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'restored' => 'Restored',
        'imported' => 'Imported',
        'exported' => 'Exported',
        'custom_action' => 'Custom Action',
      ],
      'reactivity' => false,
    ],
    'description' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'textarea',
      'label' => 'Description',
      'validation' => 'nullable|string|max:1000',
      'reactivity' => false,
    ],
    'old_values' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'json',
      'label' => 'Old Values',
      'validation' => 'nullable|json',
      'reactivity' => false,
    ],
    'new_values' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'json',
      'label' => 'New Values',
      'validation' => 'nullable|json',
      'reactivity' => false,
    ],
    'properties' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'json',
      'label' => 'Properties',
      'validation' => 'nullable|json',
      'reactivity' => false,
    ],
    'created_at' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'datetimepicker',
      'label' => 'Created At',
      'validation' => 'nullable|date',
      'reactivity' => false,
    ],
    'updated_at' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'datetimepicker',
      'label' => 'Updated At',
      'validation' => 'nullable|date',
      'reactivity' => false,
    ],
  ],
  'detailComponent' => '',
  'hiddenFields' => [
    'onTable' => [
      '0' => 'causer_type',
      '1' => 'causer_id',
      '2' => 'subject_type',
      '3' => 'subject_id',
      '4' => 'old_values',
      '5' => 'new_values',
      '6' => 'properties',
      '7' => 'updated_at',
    ],
    'onNewForm' => '*',
    'onEditForm' => '*',
    'onDetail' => [],
  ],
  'simpleActions' => [
    '0' => 'show',
  ],
  'isTransaction' => false,
  'viewType' => 'pages',
  'includeControllers' => false,
  'addRoutes' => false,
  'dispatchEvents' => false,
  'controls' => [
    'files' => [
      'export' => [
        '0' => 'xls',
        '1' => 'csv',
        '2' => 'pdf',
      ],
      'print' => true,
    ],
    'bulkActions' => [
      'export' => [
        '0' => 'xls',
        '1' => 'csv',
        '2' => 'pdf',
      ],
      'delete' => true,
    ],
    'perPage' => [
      '0' => 10,
      '1' => 25,
      '2' => 50,
      '3' => 100,
      '4' => 250,
    ],
    'search' => true,
    'showHideColumns' => true,
    'filterColumns' => true,
    'filters' => [
      '0' => [
        'field' => 'log_name',
        'type' => 'select',
        'optionsFrom' => 'distinct_log_names',
        'label' => 'Module',
      ],
      '1' => [
        'field' => 'action',
        'type' => 'select',
        'options' => [
          '0' => 'All',
          '1' => 'created',
          '2' => 'updated',
          '3' => 'deleted',
          '4' => 'approved',
          '5' => 'rejected',
          '6' => 'restored',
          '7' => 'imported',
          '8' => 'exported',
        ],
        'label' => 'Action',
      ],
      '2' => [
        'field' => 'created_at',
        'type' => 'date_range',
        'label' => 'Date Range',
      ],
      '3' => [
        'field' => 'causer_id',
        'type' => 'select',
        'optionsFrom' => 'users',
        'label' => 'User',
        'depends_on' => 'causer_type',
      ],
    ],
  ],
  'fieldGroups' => [
    'main_info' => [
      'title' => 'Activity Information',
      'groupType' => 'admin',
      'icon' => 'fas fa-info-circle',
      'fields' => [
        '0' => 'log_name',
        '1' => 'action',
        '2' => 'description',
        '3' => 'created_at',
      ],
    ],
    'subject_info' => [
      'title' => 'Affected Record',
      'groupType' => 'admin',
      'icon' => 'fas fa-database',
      'fields' => [
        '0' => 'subject_type',
        '1' => 'subject_id',
      ],
    ],
    'causer_info' => [
      'title' => 'Performed By',
      'groupType' => 'admin',
      'icon' => 'fas fa-user',
      'fields' => [
        '0' => 'causer_type',
        '1' => 'causer_id',
      ],
    ],
    'changes' => [
      'title' => 'Detailed Changes',
      'groupType' => 'admin',
      'icon' => 'fas fa-code-branch',
      'fields' => [
        '0' => 'old_values',
        '1' => 'new_values',
      ],
    ],
    'metadata' => [
      'title' => 'Metadata',
      'groupType' => 'admin',
      'icon' => 'fas fa-cog',
      'fields' => [
        '0' => 'properties',
      ],
    ],
  ],
  'moreActions' => [
    '0' => [
      'title' => 'Delete Log Entry',
      'icon' => 'fas fa-trash-alt',
      'actionName' => 'delete_log',
      'confirm' => 'Permanently delete this log entry? This action cannot be undone.',
      'requiredRole' => [
        '0' => 'admin',
        '1' => 'system_admin',
      ],
      'dispatchLivewireEvent' => true,
      'eventName' => 'deleteActivityLog',
      'params' => [
        'log_id' => '{id}',
      ],
    ],
  ],
  'switchViews' => [
    'default' => 'list',
    'card' => [
      'titleFields' => [
        '0' => 'log_name',
        '1' => 'action',
      ],
      'subtitleFields' => [
        '0' => 'created_at',
      ],
      'contentFields' => [
        '0' => 'description',
      ],
      'badgeField' => 'action',
      'badgeColors' => [
        'created' => 'success',
        'updated' => 'info',
        'deleted' => 'danger',
        'approved' => 'primary',
      ],
    ],
    'list' => [
      'titleFields' => [
        '0' => 'log_name',
        '1' => 'action',
      ],
      'subtitleFields' => [
        '0' => 'created_at',
      ],
      'contentFields' => [
        '0' => 'description',
      ],
      'badgeField' => 'action',
      'badgeColors' => [
        'created' => 'success',
        'updated' => 'info',
        'deleted' => 'danger',
        'approved' => 'primary',
      ],
    ],
    'detail' => [
      'layout' => 'tab',
      'detailType' => 'record',
      'titleFields' => [
        '0' => 'log_name',
        '1' => 'action',
      ],
      'subtitleFields' => [
        '0' => 'created_at',
      ],
      'tabs' => [
        '0' => [
          'title' => 'Overview',
          'icon' => 'fas fa-info-circle',
          'fields' => [
            '0' => 'log_name',
            '1' => 'action',
            '2' => 'description',
            '3' => 'created_at',
          ],
        ],
        '1' => [
          'title' => 'Affected Record',
          'icon' => 'fas fa-database',
          'fields' => [
            '0' => 'subject_type',
            '1' => 'subject_id',
          ],
        ],
        '2' => [
          'title' => 'User',
          'icon' => 'fas fa-user',
          'fields' => [
            '0' => 'causer_type',
            '1' => 'causer_id',
          ],
        ],
        '3' => [
          'title' => 'Changes',
          'icon' => 'fas fa-code-branch',
          'fields' => [
            '0' => 'old_values',
            '1' => 'new_values',
          ],
        ],
        '4' => [
          'title' => 'Metadata',
          'icon' => 'fas fa-cog',
          'fields' => [
            '0' => 'properties',
          ],
        ],
      ],
    ],
  ],
  'relations' => [
    'causer' => [
      'type' => 'morphTo',
      'model' => 'App\Models\User',
      'foreignKey' => '',
      'localKey' => '',
      'typeField' => 'causer_type',
      'idField' => 'causer_id',
    ],
    'subject' => [
      'type' => 'morphTo',
      'model' => 'App\Models\User',
      'foreignKey' => '',
      'localKey' => '',
      'typeField' => 'subject_type',
      'idField' => 'subject_id',
    ],
  ],
  'report' => [],
];
