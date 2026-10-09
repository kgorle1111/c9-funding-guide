<?php

/**
 * Create the Funding Award fields. Run: ddev drush php:script drupal/scripts/create_funding_award_fields.php
 * Idempotent: skips any field that already exists.
 */

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

$fields = [
  'field_organization' => [
    'type' => 'string', 'label' => 'Organization', 'required' => TRUE, 'settings' => [],
  ],
  'field_quarter' => [
    'type' => 'list_string', 'label' => 'Quarter', 'required' => TRUE,
    'settings' => ['allowed_values' => ['fall' => 'Fall', 'winter' => 'Winter', 'spring' => 'Spring']],
  ],
  'field_academic_year' => [
    'type' => 'string', 'label' => 'Academic year', 'required' => TRUE, 'settings' => [],
  ],
  'field_amount' => [
    'type' => 'decimal', 'label' => 'Amount awarded', 'required' => TRUE,
    'settings' => ['precision' => 10, 'scale' => 2],
  ],
  'field_status' => [
    'type' => 'list_string', 'label' => 'Status', 'required' => TRUE,
    'settings' => ['allowed_values' => ['awarded' => 'Awarded', 'denied' => 'Denied']],
  ],
  'field_purpose' => [
    'type' => 'string_long', 'label' => 'Event / purpose', 'required' => FALSE, 'settings' => [],
  ],
];

foreach ($fields as $name => $def) {
  if (!FieldStorageConfig::loadByName('node', $name)) {
    FieldStorageConfig::create([
      'field_name' => $name,
      'entity_type' => 'node',
      'type' => $def['type'],
      'settings' => $def['settings'],
    ])->save();
  }
  if (!FieldConfig::loadByName('node', 'funding_award', $name)) {
    FieldConfig::create([
      'field_name' => $name,
      'entity_type' => 'node',
      'bundle' => 'funding_award',
      'label' => $def['label'],
      'required' => $def['required'],
    ])->save();
    print "created $name\n";
  }
  else {
    print "exists  $name\n";
  }
}

// Show the new fields on the node form and default display.
$repo = \Drupal::service('entity_display.repository');
$form = $repo->getFormDisplay('node', 'funding_award');
$view = $repo->getViewDisplay('node', 'funding_award');
foreach (array_keys($fields) as $name) {
  $form->setComponent($name, []);
  $view->setComponent($name, ['label' => 'inline']);
}
$form->save();
$view->save();
print "form + view displays updated\n";
