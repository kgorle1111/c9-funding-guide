<?php

/**
 * Create the two content types the site needs: Basic page (with a body field)
 * and Funding Award. Run before the other scripts. Idempotent.
 *
 * The "standard" install profile on current Drupal 11 releases does not
 * always create a Basic page type, so this script never assumes it exists.
 */

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\node\Entity\NodeType;

$types = [
  'page' => ['Basic page', 'A simple page of text and images.'],
  'funding_award' => ['Funding Award', 'One funding decision by the College Nine Senate: organization, quarter, amount, and status.'],
];

foreach ($types as $id => [$name, $description]) {
  if (NodeType::load($id)) {
    print "exists  $id\n";
    continue;
  }
  NodeType::create([
    'type' => $id,
    'name' => $name,
    'description' => $description,
    'display_submitted' => FALSE,
  ])->save();
  print "created $id\n";
}

if (!FieldStorageConfig::loadByName('node', 'body')) {
  FieldStorageConfig::create(['field_name' => 'body', 'entity_type' => 'node', 'type' => 'text_with_summary'])->save();
}
if (!FieldConfig::loadByName('node', 'page', 'body')) {
  FieldConfig::create(['field_name' => 'body', 'entity_type' => 'node', 'bundle' => 'page', 'label' => 'Body'])->save();
  $repo = \Drupal::service('entity_display.repository');
  $repo->getFormDisplay('node', 'page')->setComponent('body', [])->save();
  $repo->getViewDisplay('node', 'page')->setComponent('body', ['label' => 'hidden'])->save();
  print "created page body field\n";
}
