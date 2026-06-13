<?php

return array (
  'model' => 'App\\Modules\\Hr\\Models\\LeaveRequest',
  'title' => 'Leave Request Approval',
  'description' => 'Manager and HR approval for time off',
  'lock_while_approving' => true,
  'tiers' => 
  array (
    0 => 
    array (
      'type' => 'initiation',
      'name' => 'Submit Request',
      'roles' => 
      array (
      ),
    ),
    1 => 
    array (
      'type' => 'reviewing',
      'name' => 'Team Lead Review',
      'roles' => 
      array (
        0 => 'team_lead',
      ),
      'approval_mode' => 'any',
    ),
    2 => 
    array (
      'type' => 'reviewing',
      'name' => 'HR Review',
      'roles' => 
      array (
        0 => 'hr_specialist',
      ),
      'approval_mode' => 'any',
    ),
    3 => 
    array (
      'type' => 'authorization',
      'name' => 'Department Head Authorization',
      'roles' => 
      array (
        0 => 'dept_head',
      ),
      'approval_mode' => 'any',
    ),
  ),
  'notifications' => 
  array (
    'on_submit' => 
    array (
      0 => 'approvers',
      1 => 'submitter',
    ),
    'on_approve' => 
    array (
      0 => 'next_approvers',
    ),
    'on_reject' => 
    array (
      0 => 'submitter',
    ),
  ),
  'context' => 'leave',
  'module' => 'hr',
);
