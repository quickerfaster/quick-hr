<?php

return [
  'model' => 'App\Modules\Hr\Models\Employee',
  'fieldDefinitions' => [
    'employee_number' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Employee Number',
      'validation' => 'required|unique:employees,employee_number',
      'autoGenerate' => true,
      'reactivity' => false,
      'wizard' => [
        'employee_onboarding' => true,
      ],
    ],
    'first_name' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'First Name',
      'validation' => 'required|string|max:255',
      'reactivity' => false,
      'wizard' => [
        'employee_onboarding' => true,
      ],
    ],
    'last_name' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Last Name',
      'validation' => 'required|string|max:255',
      'reactivity' => false,
      'wizard' => [
        'employee_onboarding' => true,
      ],
    ],
    'phone' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Phone',
      'validation' => 'nullable|string|max:255',
      'reactivity' => false,
    ],
    'hire_date' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'datepicker',
      'label' => 'Hire Date',
      'validation' => 'required',
      'reactivity' => false,
      'wizard' => [
        'employee_onboarding' => true,
      ],
    ],
    'department_id' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Department',
      'validation' => 'required|integer',
      'reactivity' => false,
      'relationship' => [
        'model' => 'App\Modules\Admin\Models\Department',
        'type' => 'belongsTo',
        'display_field' => 'name',
        'dynamic_property' => 'department',
        'foreign_key' => 'department_id',
        'inlineAdd' => false,
      ],
      'options' => [
        'model' => 'App\Modules\Admin\Models\Department',
        'column' => 'name',
        'hintField' => '',
      ],
      'wizard' => [
        'employee_onboarding' => true,
      ],
    ],
    'status' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Status',
      'validation' => 'nullable',
      'options' => [
        'Active' => 'Active',
        'Inactive' => 'Inactive',
        'Terminated' => 'Terminated',
      ],
      'reactivity' => false,
      'wizard' => [
        'employee_onboarding' => true,
      ],
    ],
    'date_of_birth' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'datepicker',
      'label' => 'Date Of Birth',
      'validation' => 'nullable|date',
      'reactivity' => false,
    ],
    'gender' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Gender',
      'validation' => 'required',
      'options' => [
        'Male' => 'Male',
        'Female' => 'Female',
        'Non-binary' => 'Non-binary',
        'Prefer not to say' => 'Prefer not to say',
      ],
      'reactivity' => false,
    ],
    'email' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Email',
      'validation' => 'nullable|email|unique:employees,email',
      'reactivity' => false,
      'wizard' => [
        'employee_onboarding' => true,
      ],
    ],
    'user_id' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Login Name',
      'validation' => 'nullable|unique:employees,user_id',
      'reactivity' => false,
      'relationship' => [
        'model' => 'App\Models\User',
        'type' => 'belongsTo',
        'display_field' => 'name',
        'dynamic_property' => 'user',
        'foreign_key' => 'user_id',
        'inlineAdd' => false,
      ],
      'options' => [
        'model' => 'App\Models\User',
        'column' => 'name',
        'hintField' => 'email',
      ],
    ],
    'nationality' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Nationality',
      'validation' => 'nullable|string|max:255',
      'reactivity' => false,
    ],
    'marital_status' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'select',
      'label' => 'Marital Status',
      'validation' => 'nullable|string',
      'options' => [
        'Single' => 'Single',
        'Married' => 'Married',
        'Divorced' => 'Divorced',
        'Widowed' => 'Widowed',
      ],
      'reactivity' => false,
    ],
    'address_street' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Address Street',
      'validation' => 'nullable|string|max:255',
      'reactivity' => false,
    ],
    'address_city' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Address City',
      'validation' => 'nullable|string|max:255',
      'reactivity' => false,
    ],
    'address_state' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Address State',
      'validation' => 'nullable|string|max:255',
      'reactivity' => false,
    ],
    'address_postal_code' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Address Postal Code',
      'validation' => 'nullable|string|max:255',
      'reactivity' => false,
    ],
    'address_country' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Address Country',
      'validation' => 'nullable|string|max:255',
      'reactivity' => false,
    ],
  ],





'drawers' => [
    'filter_drawer' => [
        'label' => 'Filters',
        'icon' => 'fas fa-filter',
        'component' => 'qf.filter-panel', // Livewire component name
        'params' => ['configKey' => '{configKey}'],
        'size' => 'md', // or 'lg'
    ],
    'quick_add' => [
        'label' => 'Quick Add Employee',
        'icon' => 'fas fa-plus',
        'component' => 'qf.data-table-form',
        'params' => [
            'configKey' => '{configKey}',
            'inline' => true,
            'modalId' => null, // not used for drawer, but fine
        ],
        'size' => 'lg',
    ],
],










  'detailComponent' => 'qf.employee-detail',
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
  'viewType' => 'pages',
  'includeControllers' => false,
  'addRoutes' => false,
  'dispatchEvents' => false,
  'controls' => [
    'addButton' => [
      '0' => [
        'label' => 'Add Employee',
        'type' => 'quick_add',
        'icon' => 'fas fa-plus',
        'primary' => true,
      ],
      '1' => [
        'label' => 'Onboard New Hire (Guided)',
        'type' => 'wizard',
        'url' => '/hr/employee-onboarding',
        'wizard' => 'employee_onboarding',
        'icon' => 'fas fa-user-plus',
      ],
    ],
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
      '0' => 5,
      '1' => 10,
      '2' => 25,
      '3' => 50,
      '4' => 100,
      '5' => 200,
      '6' => 500,
    ],
    'search' => true,
    'showHideColumns' => true,
  ],
  'fieldGroups' => [
    'personal_information' => [
      'title' => 'Personal Information',
      'groupType' => 'hr',
      'fields' => [
        '0' => 'first_name',
        '1' => 'last_name',
        '2' => 'date_of_birth',
        '3' => 'gender',
        '4' => 'marital_status',
        '5' => 'nationality',
      ],
    ],
    'contact_information' => [
      'title' => 'Contact Information',
      'groupType' => 'hr',
      'fields' => [
        '0' => 'phone',
        '1' => 'address_street',
        '2' => 'address_city',
        '3' => 'address_state',
        '4' => 'address_postal_code',
        '5' => 'address_country',
      ],
    ],
    'employment_details' => [
      'title' => 'Employment Details',
      'groupType' => 'hr',
      'fields' => [
        '0' => 'employee_number',
        '1' => 'email',
        '2' => 'user_id',
        '3' => 'hire_date',
        '4' => 'department_id',
        '5' => 'status',
      ],
    ],
  ],
  'moreActions' => [],
  'switchViews' => [
    'default' => 'list',
    'card' => [
      'titleFields' => [
        '0' => 'first_name',
        '1' => 'last_name',
      ],
      'subtitleFields' => [
        '0' => 'employee_number',
        '1' => 'department.name',
      ],
      'contentFields' => [
        '0' => 'email',
        '1' => 'gender',
        '2' => 'hire_date',
      ],
      'badgeField' => 'status',
      'badgeColors' => [
        'Active' => 'success',
        'Inctive' => 'secondary',
      ],
    ],
    'list' => [
      'titleFields' => [
        '0' => 'first_name',
        '1' => 'last_name',
      ],
      'subtitleFields' => [
        '0' => 'employee_number',
        '1' => 'department.name',
      ],
      'contentFields' => [
        '0' => 'email',
        '1' => 'gender',
        '2' => 'hire_date',
      ],
      'badgeField' => 'status',
      'badgeColors' => [
        'Active' => 'success',
        'Inctive' => 'secondary',
      ],
    ],
    'detail' => [
      'layout' => 'tab',
      'detailType' => 'profile',
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
      'headerLink' => [
        '0' => [
          'label' => 'Documents',
        ],
        '1' => [
          'url' => '/hr/documents?employee_id={id}',
        ],
        '2' => [
          'returnTo' => '/hr/employees',
        ],
      ],
    ],
  ],
  'relations' => [
    'department' => [
      'type' => 'belongsTo',
      'model' => 'App\Modules\Admin\Models\Department',
      'foreignKey' => 'department_id',
      'localKey' => '',
    ],
    'documents' => [
      'type' => 'hasMany',
      'model' => 'App\Modules\Hr\Models\Document',
      'foreignKey' => 'employee_id',
      'localKey' => '',
    ],
    'employeeWorkPatterns' => [
      'type' => 'hasMany',
      'model' => 'App\Modules\Hr\Models\EmployeeWorkPattern',
      'foreignKey' => 'employee_id',
      'localKey' => '',
    ],
    'employeeProfile' => [
      'type' => 'hasOne',
      'model' => 'App\Modules\Hr\Models\EmployeeProfile',
      'foreignKey' => 'employee_id',
      'localKey' => '',
    ],
    'employeePosition' => [
      'type' => 'hasOne',
      'model' => 'App\Modules\Hr\Models\EmployeePosition',
      'foreignKey' => 'employee_id',
      'localKey' => '',
    ],
    'user' => [
      'type' => 'belongsTo',
      'model' => 'App\Models\User',
      'foreignKey' => 'user_id',
      'localKey' => '',
    ],
  ],
  'report' => [],
];
