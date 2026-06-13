<?php

return array (
  'model' => 'App\\Modules\\Hr\\Models\\PayrollRun',
  'title' => 'Payroll Run Approval',
  'description' => 'Review and authorize payroll calculations',
  'lock_while_approving' => false,
  'tiers' => 
  array (
    0 => 
    array (
      'type' => 'reviewing',
      'name' => 'Payroll Officer Review',
      'roles' => 
      array (
        0 => 'payroll_officer',
      ),
      'approval_mode' => 'any',
    ),
    1 => 
    array (
      'type' => 'authorization',
      'name' => 'HR Manager Authorization',
      'roles' => 
      array (
        0 => 'hr_manager',
      ),
      'approval_mode' => 'any',
    ),
  ),
  'notifications' => 
  array (
  ),
  'context' => 'payroll',
  'module' => 'hr',
);
