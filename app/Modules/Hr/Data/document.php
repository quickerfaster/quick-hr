<?php

return [
  'model' => 'App\Modules\Hr\Models\Document',
  'fieldDefinitions' => [
    'employee_id' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'livewire-searchable-select',
      'label' => 'Employee Number',
      'validation' => 'required|unique:documents,employee_id',
      'reactivity' => false,
      'relationship' => [
        'model' => 'App\Modules\Hr\Models\Employee',
        'type' => 'belongsTo',
        'display_field' => 'employee_number',
        'dynamic_property' => 'employee',
        'foreign_key' => 'employee_id',
        'inlineAdd' => false,
      ],
      'options' => [
        'model' => 'App\Modules\Hr\Models\Employee',
        'column' => 'employee_number',
        'hintField' => 'first_name',
      ],
    ],
    'document' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'file',
      'label' => 'Document',
      'validation' => 'required|file|max:1024',
      'reactivity' => false,
    ],
    'name' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Document Name',
      'validation' => 'required|string|max:255',
      'reactivity' => false,
    ],
    'type' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Type',
      'validation' => 'required',
      'options' => [
        'Resume' => 'Resume',
        'Contract' => 'Contract',
        'Offer Letter' => 'Offer Letter',
        'ID Proof' => 'ID Proof',
        'Visa' => 'Visa',
        'Certificate' => 'Certificate',
        'Performance Review' => 'Performance Review',
        'Other' => 'Other',
      ],
      'reactivity' => false,
    ],
    'uploaded_at' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'datepicker',
      'label' => 'Uploaded At',
      'validation' => 'nullable|date',
      'reactivity' => false,
    ],
    'expiry_date' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'datepicker',
      'label' => 'Expiry Date',
      'validation' => 'nullable|date',
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
    'onNewForm' => [
      '0' => 'uploaded_at',
    ],
    'onEditForm' => [
      '0' => 'uploaded_at',
    ],
    'onQuery' => [],
  ],
  'simpleActions' => [
    '0' => 'show',
    '1' => 'edit',
    '2' => 'delete',
  ],
  'isTransaction' => false,
  'viewType' => 'pages',
  'includeControllers' => false,
  'addRoutes' => false,
  'dispatchEvents' => false,
  'controls' => 'all',
  'fieldGroups' => [
    'document_details' => [
      'title' => 'Document Details',
      'groupType' => 'hr',
      'fields' => [
        '0' => 'employee_id',
        '1' => 'name',
        '2' => 'type',
        '3' => 'description',
      ],
    ],
    'file_information' => [
      'title' => 'File Information',
      'groupType' => 'hr',
      'fields' => [
        '0' => 'document',
        '1' => 'uploaded_at',
        '2' => 'expiry_date',
      ],
    ],
  ],
  'moreActions' => [],
  'switchViews' => [
    'detail' => [
      'layout' => 'tab',
      'detailType' => 'document',
      'imageField' => [
        '0' => 'photo',
      ],
      'icon' => 'fas fa-user-profile',
      'titleFields' => [
        '0' => 'first_name',
        '1' => 'last_name',
      ],
      'subtitleFields' => [
        '0' => 'email',
        '1' => 'phone',
      ],
      'contentFields' => [
        '0' => 'gender',
        '1' => 'date_of_birth',
      ],
    ],
  ],
  'relations' => [
    'employee' => [
      'type' => 'belongsTo',
      'model' => 'App\Modules\Hr\Models\Employee',
      'foreignKey' => 'employee_id',
      'localKey' => '',
    ],
  ],
  'report' => [],
];
