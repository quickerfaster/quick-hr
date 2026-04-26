<?php

return array (
  'module' => 'hr',
  'context' => 'people',
  'configKey' => 'hr_employee',
  'model' => 'App\\Modules\\Hr\\Models\\Employee',
  'title' => 'Employee Summary Dashboard',
  'label' => 'Employee Summary Dashboard',
  'type' => 'dashboard',
  'description' => 'Key HR metrics',
  'widgets' => 
  array (
    0 => 
    array (
      'width' => 3,
      'type' => 'stat',
      'title' => 'Total Employees',
      'model' => 'App\\Modules\\Hr\\Models\\Employee',
      'aggregate' => 'count',
    ),
    1 => 
    array (
      'width' => 3,
      'type' => 'stat',
      'title' => 'Departments',
      'model' => 'App\\Modules\\Hr\\Models\\Department',
      'aggregate' => 'count',
    ),
    2 => 
    array (
      'width' => 6,
      'type' => 'chart',
      'title' => 'Employees by Status',
      'model' => 'App\\Modules\\Hr\\Models\\Employee',
      'group_by' => 'status',
      'chart_type' => 'pie',
    ),
  ),
  'layout' => 
  array (
    'columns' => 12,
    'gutter' => 3,
  ),
);
