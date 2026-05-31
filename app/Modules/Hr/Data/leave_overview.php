<?php

return [
  'model' => 'App\Modules\Hr\Models\LeaveOverview',
  'fieldDefinitions' => [
    'dummy' => [
      'display' => 'inline',
      'fillable' => true,
      'field_type' => 'string',
      'label' => 'Dummy',
      'validation' => 'nullable|string|max:255',
    ],
  ],
  'detailComponent' => '',
  'hiddenFields' => [],
  'simpleActions' => [],
  'isTransaction' => false,
  'crudType' => 'modals',
  'includeControllers' => false,
  'tableDefaultFields' => [],
  'addRoutes' => false,
  'dispatchEvents' => false,
  'controls' => [],
  'fieldGroups' => [],
  'moreActions' => [],
  'switchViews' => [],
  'relations' => [],
  'report' => [],
];
