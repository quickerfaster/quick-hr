<?php

return array (
  'title' => 'Organization Settings Overview',
  'description' => 'Company structure, locations, departments, and job titles',
  'widgets' => 
  array (
    0 => 
    array (
      'type' => 'stat',
      'title' => 'Total Locations',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Admin\\Models\\Location',
      'icon' => 'fas fa-map-marker-alt',
      'aggregate' => 'count',
      'width' => 3,
    ),
    1 => 
    array (
      'type' => 'stat',
      'title' => 'Active Locations',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Admin\\Models\\Location',
      'icon' => 'fas fa-check-circle',
      'aggregate' => 'count',
      'conditions' => 
      array (
        0 => 
        array (
          0 => 'is_active',
          1 => '=',
          2 => true,
        ),
      ),
      'width' => 3,
    ),
    2 => 
    array (
      'type' => 'stat',
      'title' => 'Departments',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Admin\\Models\\Department',
      'icon' => 'fas fa-sitemap',
      'aggregate' => 'count',
      'width' => 3,
    ),
    3 => 
    array (
      'type' => 'stat',
      'title' => 'Job Titles',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Admin\\Models\\JobTitle',
      'icon' => 'fas fa-briefcase',
      'aggregate' => 'count',
      'width' => 3,
    ),
    4 => 
    array (
      'type' => 'chart',
      'title' => 'Locations by Country',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Admin\\Models\\Location',
      'group_by' => 'country',
      'chart_type' => 'bar',
      'aggregate' => 'count',
      'conditions' => 
      array (
        0 => 
        array (
          0 => 'is_active',
          1 => '=',
          2 => true,
        ),
      ),
      'width' => 4,
    ),
    5 => 
    array (
      'type' => 'chart',
      'title' => 'Locations by Type',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Admin\\Models\\Location',
      'group_by' => 'is_remote',
      'chart_type' => 'pie',
      'aggregate' => 'count',
      'width' => 4,
    ),
    6 => 
    array (
      'type' => 'chart',
      'title' => 'Departments by Company',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Admin\\Models\\Department',
      'group_by' => 'company_id',
      'chart_type' => 'bar',
      'aggregate' => 'count',
      'width' => 4,
    ),
    7 => 
    array (
      'type' => 'list',
      'title' => 'Recent Locations',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Admin\\Models\\Location',
      'icon' => 'fas fa-building',
      'description' => 'Latest 5 added locations',
      'limit' => 5,
      'sort' => 
      array (
        0 => 'created_at',
        1 => 'desc',
      ),
      'columns' => 
      array (
        0 => 
        array (
          'label' => 'Name',
          'field' => 'name',
        ),
        1 => 
        array (
          'label' => 'City',
          'field' => 'city',
        ),
        2 => 
        array (
          'label' => 'Country',
          'field' => 'country',
        ),
        3 => 
        array (
          'label' => 'Status',
          'field' => 'is_active',
          'format' => 'boolean',
        ),
      ),
      'width' => 6,
      'show_view_all' => true,
      'view_all_link' => '/admin/locations',
    ),
    8 => 
    array (
      'type' => 'list',
      'title' => 'Departments',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Admin\\Models\\Department',
      'icon' => 'fas fa-users',
      'description' => 'All departments (alphabetical)',
      'limit' => 5,
      'sort' => 
      array (
        0 => 'name',
        1 => 'asc',
      ),
      'columns' => 
      array (
        0 => 
        array (
          'label' => 'Name',
          'field' => 'name',
        ),
        1 => 
        array (
          'label' => 'Code',
          'field' => 'code',
        ),
        2 => 
        array (
          'label' => 'Company',
          'field' => 'company.name',
        ),
      ),
      'width' => 6,
      'show_view_all' => true,
      'view_all_link' => '/admin/departments',
    ),
    9 => 
    array (
      'type' => 'action_card',
      'title' => 'Add New Location',
      'size' => 'col-12',
      'icon' => 'fas fa-map-pin',
      'description' => 'Create a new office or branch',
      'actions' => 
      array (
        0 => 
        array (
          'label' => 'Create',
          'event' => 'openLocationWizard',
          'params' => 
          array (
            'type' => 'location',
          ),
          'style' => 'primary',
        ),
      ),
      'width' => 3,
    ),
    10 => 
    array (
      'type' => 'action_card',
      'title' => 'Add Department',
      'size' => 'col-12',
      'icon' => 'fas fa-folder-open',
      'description' => 'Create a new department',
      'actions' => 
      array (
        0 => 
        array (
          'label' => 'Create',
          'event' => 'openDepartmentWizard',
          'params' => 
          array (
            'type' => 'department',
          ),
          'style' => 'secondary',
        ),
      ),
      'width' => 3,
    ),
    11 => 
    array (
      'type' => 'action_card',
      'title' => 'Manage Companies',
      'size' => 'col-12',
      'icon' => 'fas fa-building',
      'description' => 'Configure company entities',
      'actions' => 
      array (
        0 => 
        array (
          'label' => 'View',
          'event' => 'navigate',
          'params' => 
          array (
            'url' => '/admin/companies',
          ),
          'style' => 'secondary',
        ),
      ),
      'width' => 3,
    ),
    12 => 
    array (
      'type' => 'action_card',
      'title' => 'Job Titles',
      'size' => 'col-12',
      'icon' => 'fas fa-tag',
      'description' => 'Define job roles and titles',
      'actions' => 
      array (
        0 => 
        array (
          'label' => 'Manage',
          'event' => 'navigate',
          'params' => 
          array (
            'url' => '/admin/job-titles',
          ),
          'style' => 'secondary',
        ),
      ),
      'width' => 3,
    ),
    13 => 
    array (
      'type' => 'list',
      'title' => 'Companies',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Admin\\Models\\Company',
      'icon' => 'fas fa-flag',
      'description' => 'Active companies in the system',
      'limit' => 5,
      'sort' => 
      array (
        0 => 'name',
        1 => 'asc',
      ),
      'conditions' => 
      array (
        0 => 
        array (
          0 => 'status',
          1 => '=',
          2 => 'active',
        ),
      ),
      'columns' => 
      array (
        0 => 
        array (
          'label' => 'Name',
          'field' => 'name',
        ),
        1 => 
        array (
          'label' => 'Level',
          'field' => 'level',
        ),
        2 => 
        array (
          'label' => 'Status',
          'field' => 'status',
        ),
      ),
      'width' => 6,
      'show_view_all' => true,
      'view_all_link' => '/admin/companies',
    ),
    14 => 
    array (
      'type' => 'list',
      'title' => 'Job Titles',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Admin\\Models\\JobTitle',
      'icon' => 'fas fa-briefcase',
      'description' => 'Recent job titles',
      'limit' => 5,
      'sort' => 
      array (
        0 => 'created_at',
        1 => 'desc',
      ),
      'columns' => 
      array (
        0 => 
        array (
          'label' => 'Title',
          'field' => 'title',
        ),
        1 => 
        array (
          'label' => 'Description',
          'field' => 'description',
          'truncate' => 50,
        ),
      ),
      'width' => 6,
      'show_view_all' => true,
      'view_all_link' => '/admin/job-titles',
    ),
    15 => 
    array (
      'type' => 'progress',
      'title' => 'Organization Setup Completion',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Admin\\Models\\Location',
      'icon' => 'fas fa-check-double',
      'description' => 'Locations with complete address',
      'aggregate' => 'count',
      'conditions' => 
      array (
        0 => 
        array (
          0 => 'address_line_1',
          1 => '!=',
          2 => NULL,
        ),
        1 => 
        array (
          0 => 'city',
          1 => '!=',
          2 => NULL,
        ),
      ),
      'target_model' => 'App\\Modules\\Admin\\Models\\Location',
      'target_aggregate' => 'count',
      'width' => 3,
    ),
  ),
);
