<?php

return array (
  'title' => 'Time & Attendance Overview',
  'description' => 'Attendance tracking, shift schedules, and holiday management',
  'widgets' => 
  array (
    0 => 
    array (
      'type' => 'stat',
      'title' => 'Today\'s Attendance',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Hr\\Models\\Attendance',
      'icon' => 'fas fa-user-check',
      'aggregate' => 'count',
      'conditions' => 
      array (
        0 => 
        array (
          0 => 'date',
          1 => '=',
          2 => 'today',
        ),
        1 => 
        array (
          0 => 'status',
          1 => '!=',
          2 => 'absent',
        ),
      ),
      'width' => 3,
    ),
    1 => 
    array (
      'type' => 'stat',
      'title' => 'Absent Today',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Hr\\Models\\Attendance',
      'icon' => 'fas fa-user-times',
      'aggregate' => 'count',
      'conditions' => 
      array (
        0 => 
        array (
          0 => 'date',
          1 => '=',
          2 => 'today',
        ),
        1 => 
        array (
          0 => 'status',
          1 => '=',
          2 => 'absent',
        ),
      ),
      'width' => 3,
    ),
    2 => 
    array (
      'type' => 'stat',
      'title' => 'Pending Approvals',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Hr\\Models\\Attendance',
      'icon' => 'fas fa-clock',
      'aggregate' => 'count',
      'conditions' => 
      array (
        0 => 
        array (
          0 => 'is_approved',
          1 => '=',
          2 => false,
        ),
        1 => 
        array (
          0 => 'date',
          1 => '>=',
          2 => 'first day of this month',
        ),
      ),
      'width' => 3,
    ),
    3 => 
    array (
      'type' => 'stat',
      'title' => 'Upcoming Holidays',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Hr\\Models\\Holiday',
      'icon' => 'fas fa-gift',
      'aggregate' => 'count',
      'conditions' => 
      array (
        0 => 
        array (
          0 => 'date',
          1 => '>=',
          2 => 'today',
        ),
        1 => 
        array (
          0 => 'date',
          1 => '<=',
          2 => '+30 days',
        ),
        2 => 
        array (
          0 => 'is_active',
          1 => '=',
          2 => true,
        ),
      ),
      'width' => 3,
    ),
    4 => 
    array (
      'type' => 'chart',
      'title' => 'Attendance Status (This Month)',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Hr\\Models\\Attendance',
      'group_by' => 'status',
      'chart_type' => 'pie',
      'aggregate' => 'count',
      'conditions' => 
      array (
        0 => 
        array (
          0 => 'date',
          1 => '>=',
          2 => 'first day of this month',
        ),
      ),
      'width' => 4,
    ),
    5 => 
    array (
      'type' => 'trend',
      'title' => 'Attendance Trend',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Hr\\Models\\Attendance',
      'group_by' => 'month',
      'icon' => 'fas fa-chart-line',
      'aggregate' => 'count',
      'date_field' => 'date',
      'period' => 6,
      'width' => 5,
    ),
    6 => 
    array (
      'type' => 'action_card',
      'title' => 'Quick Actions',
      'size' => 'col-12',
      'icon' => 'fas fa-clock',
      'description' => 'Clock in/out or manage schedules',
      'actions' => 
      array (
        0 => 
        array (
          'label' => 'Clock In',
          'event' => 'openClockModal',
          'params' => 
          array (
            'action' => 'clock_in',
          ),
          'style' => 'primary',
        ),
        1 => 
        array (
          'label' => 'Clock Out',
          'event' => 'openClockModal',
          'params' => 
          array (
            'action' => 'clock_out',
          ),
          'style' => 'secondary',
        ),
      ),
      'width' => 3,
    ),
    7 => 
    array (
      'type' => 'list',
      'title' => 'Recent Attendance',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Hr\\Models\\Attendance',
      'icon' => 'fas fa-calendar-alt',
      'description' => 'Latest 5 records',
      'limit' => 5,
      'sort' => 
      array (
        0 => 'date',
        1 => 'desc',
      ),
      'columns' => 
      array (
        0 => 
        array (
          'label' => 'Employee',
          'field' => 'employee.employee_number',
        ),
        1 => 
        array (
          'label' => 'Date',
          'field' => 'date',
          'format' => 'date',
        ),
        2 => 
        array (
          'label' => 'Status',
          'field' => 'status',
        ),
        3 => 
        array (
          'label' => 'Net Hours',
          'field' => 'net_hours',
        ),
      ),
      'width' => 6,
      'show_view_all' => true,
      'view_all_link' => '/hr/attendances',
    ),
    8 => 
    array (
      'type' => 'list',
      'title' => 'Upcoming Holidays',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Hr\\Models\\Holiday',
      'icon' => 'fas fa-calendar-week',
      'description' => 'Next 5 holidays',
      'limit' => 5,
      'sort' => 
      array (
        0 => 'date',
        1 => 'asc',
      ),
      'conditions' => 
      array (
        0 => 
        array (
          0 => 'date',
          1 => '>=',
          2 => 'today',
        ),
        1 => 
        array (
          0 => 'is_active',
          1 => '=',
          2 => true,
        ),
      ),
      'columns' => 
      array (
        0 => 
        array (
          'label' => 'Holiday',
          'field' => 'name',
        ),
        1 => 
        array (
          'label' => 'Date',
          'field' => 'date',
          'format' => 'date',
        ),
        2 => 
        array (
          'label' => 'Type',
          'field' => 'holiday_type',
        ),
      ),
      'width' => 6,
      'show_view_all' => true,
      'view_all_link' => '/hr/holidays',
    ),
    9 => 
    array (
      'type' => 'progress',
      'title' => 'Attendance Completion',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Hr\\Models\\Attendance',
      'icon' => 'fas fa-chart-simple',
      'description' => '% of employees with attendance recorded today',
      'aggregate' => 'count',
      'conditions' => 
      array (
        0 => 
        array (
          0 => 'date',
          1 => '=',
          2 => 'today',
        ),
        1 => 
        array (
          0 => 'status',
          1 => '!=',
          2 => 'absent',
        ),
      ),
      'target_model' => 'App\\Modules\\Hr\\Models\\Employee',
      'target_aggregate' => 'count',
      'target_conditions' => 
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
    10 => 
    array (
      'type' => 'list',
      'title' => 'Today\'s Schedules',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Hr\\Models\\ShiftSchedule',
      'icon' => 'fas fa-calendar-check',
      'description' => 'Shifts scheduled for today',
      'limit' => 10,
      'sort' => 
      array (
        0 => 'schedule_date',
        1 => 'asc',
      ),
      'conditions' => 
      array (
        0 => 
        array (
          0 => 'schedule_date',
          1 => '=',
          2 => 'today',
        ),
      ),
      'columns' => 
      array (
        0 => 
        array (
          'label' => 'Employee',
          'field' => 'employee.employee_number',
        ),
        1 => 
        array (
          'label' => 'Shift',
          'field' => 'shift.name',
        ),
        2 => 
        array (
          'label' => 'Status',
          'field' => 'status',
        ),
      ),
      'width' => 6,
      'show_view_all' => true,
      'view_all_link' => '/hr/shift-schedules',
    ),
    11 => 
    array (
      'type' => 'chart',
      'title' => 'Overtime vs Regular Hours',
      'size' => 'col-12',
      'model' => 'App\\Modules\\Hr\\Models\\Attendance',
      'chart_type' => 'bar',
      'group_by' => NULL,
      'aggregates' => 
      array (
        'regular_hours' => 'sum',
        'overtime_hours' => 'sum',
      ),
      'conditions' => 
      array (
        0 => 
        array (
          0 => 'date',
          1 => '>=',
          2 => 'first day of this month',
        ),
      ),
      'width' => 4,
    ),
  ),
  'roles' => 
  array (
    'admin' => 'full',
    'manager' => 'limited',
    'payroll_officer' => 'limited',
    'employee' => 'basic',
  ),
  'layout' => 
  array (
    'columns' => 12,
    'gutter' => 3,
  ),
);
