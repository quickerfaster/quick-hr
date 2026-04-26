<?php

return array (
  'module' => 'hr',
  'context' => 'people',
  'configKey' => 'hr_employee',
  'title' => 'Headcount & Workforce Demographics',
  'label' => 'Headcount & Demographics',
  'type' => 'dashboard',
  'description' => 'Total active employees, full‑time/part‑time split, and diversity data by department, gender, age group.',
  'widgets' => 
  array (
    0 => 
    array (
      'width' => 3,
      'type' => 'headcount_vs_budget',
      'title' => 'Budgeted headcount',
      'budgeted_headcount' => 1000,
    ),
    1 => 
    array (
      'width' => 3,
      'type' => 'stat',
      'title' => 'Total Active Employees',
      'model' => 'App\\Modules\\Hr\\Models\\Employee',
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
    ),
    2 => 
    array (
      'width' => 2,
      'type' => 'stat',
      'title' => 'Full‑Time',
      'model' => 'App\\Modules\\Hr\\Models\\Employee',
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
    ),
    3 => 
    array (
      'width' => 2,
      'type' => 'stat',
      'title' => 'Part‑Time',
      'model' => 'App\\Modules\\Hr\\Models\\Employee',
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
    ),
    4 => 
    array (
      'width' => 2,
      'type' => 'stat',
      'title' => 'Contractors',
      'model' => 'App\\Modules\\Hr\\Models\\Employee',
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
    ),
    5 => 
    array (
      'width' => 6,
      'type' => 'chart',
      'title' => 'Employees by Department',
      'model' => 'App\\Modules\\Hr\\Models\\Employee',
      'group_by' => 'department_id',
      'chart_type' => 'bar',
      'conditions' => 
      array (
        0 => 
        array (
          0 => 'status',
          1 => '=',
          2 => 'Active',
        ),
      ),
    ),
    6 => 
    array (
      'width' => 6,
      'type' => 'chart',
      'title' => 'Gender Distribution',
      'model' => 'App\\Modules\\Hr\\Models\\Employee',
      'group_by' => 'gender',
      'chart_type' => 'pie',
      'conditions' => 
      array (
        0 => 
        array (
          0 => 'status',
          1 => '=',
          2 => 'Active',
        ),
      ),
    ),
    7 => 
    array (
      'width' => 6,
      'type' => 'chart',
      'title' => 'Age Groups',
      'model' => 'App\\Modules\\Hr\\Models\\Employee',
      'chart_type' => 'bar',
      'conditions' => 
      array (
        0 => 
        array (
          0 => 'status',
          1 => '=',
          2 => 'Active',
        ),
      ),
    ),
  ),
  'layout' => 
  array (
    'columns' => 12,
    'gutter' => 3,
  ),
);
