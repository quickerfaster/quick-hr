<?php

return array (
  'title' => 'People Management Overview',
  'description' => 'Employee statistics, hiring trends, and team analytics',
  'widgets' => 
  array (
    0 => 
    array (
      'type' => 'stat',
      'title' => 'Total Employees',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Hr\\Models\\Employee',
      'icon' => 'fas fa-users',
      'aggregate' => 'count',
      'conditions' => 
      array (
        0 => 
        array (
          0 => 'status',
          1 => '=',
          2 => 'Active',
        ),
      ),
      'width' => 3,
    ),
    1 => 
    array (
      'type' => 'stat',
      'title' => 'Total Profiles',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Hr\\Models\\EmployeeProfile',
      'icon' => 'fas fa-user-circle',
      'aggregate' => 'count',
      'width' => 3,
    ),
    2 => 
    array (
      'type' => 'action_card',
      'title' => 'Process Payroll',
      'size' => 'col-12',
      'icon' => 'fas fa-calculator',
      'description' => 'Run monthly payroll for all employees.',
      'actions' => 
      array (
        0 => 
        array (
          'label' => 'Start',
          'event' => 'openPayrollWizard',
          'params' => 
          array (
            'month' => 'current',
          ),
          'style' => 'primary',
        ),
      ),
      'width' => 3,
    ),
    3 => 
    array (
      'type' => 'list',
      'title' => 'Recent Employees',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Hr\\Models\\Employee',
      'icon' => 'fas fa-user-plus',
      'description' => 'Latest 5 hires',
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
          'field' => 'first_name',
          'format' => 'text',
        ),
        1 => 
        array (
          'label' => 'Email',
          'field' => 'email',
        ),
        2 => 
        array (
          'label' => 'Department',
          'field' => 'department.name',
        ),
        3 => 
        array (
          'label' => 'Hire Date',
          'field' => 'hire_date',
          'format' => 'date',
        ),
      ),
      'width' => 6,
      'show_view_all' => true,
      'view_all_link' => '/hr/employees',
    ),
    4 => 
    array (
      'type' => 'progress',
      'title' => 'Onboarding Completion',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Hr\\Models\\Employee',
      'icon' => 'fas fa-chalkboard-teacher',
      'aggregate' => 'count',
      'conditions' => 
      array (
        0 => 
        array (
          0 => 'status',
          1 => '=',
          2 => 'Active',
        ),
      ),
      'target' => 100,
      'width' => 3,
    ),
    5 => 
    array (
      'type' => 'trend',
      'title' => 'New Hires Trend',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Hr\\Models\\Employee',
      'group_by' => 'month',
      'icon' => 'fas fa-chart-line',
      'aggregate' => 'count',
      'date_field' => 'hire_date',
      'period' => 6,
      'width' => 6,
    ),
    6 => 
    array (
      'type' => 'chart',
      'title' => 'Employees by Department',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Hr\\Models\\Employee',
      'group_by' => 'department_id',
      'chart_type' => 'bar',
      'aggregate' => 'count',
      'width' => 6,
    ),
    7 => 
    array (
      'type' => 'onboarding',
      'title' => 'Complete Your Setup',
      'size' => 'col-12',
      'icon' => 'fas fa-tasks',
      'description' => 'Finish these steps to get the most out of your workspace',
      'width' => 6,
    ),
  ),
  'roles' => 
  array (
    'admin' => 'full',
    'manager' => 'limited',
    'user' => 'basic',
  ),
  'layout' => 
  array (
    'columns' => 12,
    'gutter' => 3,
  ),
);
