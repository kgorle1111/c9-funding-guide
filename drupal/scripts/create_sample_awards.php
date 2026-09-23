<?php

/**
 * Sample Funding Award nodes (FICTIONAL orgs/amounts — public demo data pending
 * board approval of real figures). Run:
 *   ddev drush php:script drupal/scripts/create_sample_awards.php
 * Idempotent: skips if any funding_award nodes already exist.
 */

use Drupal\node\Entity\Node;

$existing = \Drupal::entityQuery('node')->condition('type', 'funding_award')->accessCheck(FALSE)->count()->execute();
if ($existing > 0) {
  print "skipped: $existing funding_award nodes already exist\n";
  return;
}

// org, quarter, year, amount, status, purpose
$rows = [
  ['Redwood Robotics Club', 'fall', '2025–26', 200.00, 'awarded', 'Parts for the winter build showcase'],
  ['Slug Salsa Collective', 'fall', '2025–26', 175.00, 'awarded', 'Fall social dance night at the C9/JRL lounge'],
  ['Coastal Photography Society', 'fall', '2025–26', 120.00, 'awarded', 'Printing for the quarter-end gallery wall'],
  ['Mock Trial at Nine', 'fall', '2025–26', 200.00, 'awarded', 'Regional tournament registration'],
  ['Anime & Manga Circle', 'fall', '2025–26', 0.00, 'denied', 'Catering request — food costs are not funded'],
  ['Global Cultures Night Committee', 'winter', '2025–26', 350.00, 'awarded', 'Venue and decorations for Global Cultures Night (justified above limit)'],
  ['Slug Cinema Club', 'winter', '2025–26', 150.00, 'awarded', 'Licensing for two public film screenings'],
  ['Women in STEM Nine', 'winter', '2025–26', 200.00, 'awarded', 'Panel night with alumni speakers'],
  ['Trail Runners of C9', 'winter', '2025–26', 90.00, 'awarded', 'Course markers and first-aid kits for campus 5K'],
  ['Esports at Nine', 'winter', '2025–26', 0.00, 'denied', 'Travel request below 25% C9 affiliate threshold'],
  ['Community Garden Crew', 'spring', '2025–26', 180.00, 'awarded', 'Raised-bed soil and seedlings for spring planting'],
  ['C9 Debate Union', 'spring', '2025–26', 200.00, 'awarded', 'Spring invitational hosting materials'],
  ['Mural Arts Project', 'spring', '2025–26', 240.00, 'awarded', 'Paint and sealant for the courtyard mural (justified above limit)'],
  ['Board Game Guild', 'spring', '2025–26', 110.00, 'awarded', 'Library expansion for weekly game nights'],
  ['Slug Improv Troupe', 'spring', '2025–26', 130.00, 'awarded', 'Spring showcase staging and props'],
];

foreach ($rows as [$org, $quarter, $year, $amount, $status, $purpose]) {
  Node::create([
    'type' => 'funding_award',
    'title' => "$org — " . ucfirst($quarter) . " $year",
    'field_organization' => $org,
    'field_quarter' => $quarter,
    'field_academic_year' => $year,
    'field_amount' => $amount,
    'field_status' => $status,
    'field_purpose' => $purpose,
    'status' => 1,
  ])->save();
  print "created: $org ($quarter, \$$amount, $status)\n";
}
print count($rows) . " awards created\n";
