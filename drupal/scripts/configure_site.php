<?php

/**
 * Site settings that live in configuration rather than content: places the
 * transparency dashboard, hides the login menu, turns off the default logo,
 * and disables CSS/JS aggregation for local development. Run after the
 * c9_senate theme is enabled. Idempotent.
 */

use Drupal\block\Entity\Block;

if (!Block::load('c9_transparency_block')) {
  Block::create([
    'id' => 'c9_transparency_block',
    'plugin' => 'c9_transparency',
    'theme' => 'c9_senate',
    'region' => 'content',
    'weight' => 10,
    'settings' => ['label' => 'Funding Transparency', 'label_display' => '0'],
    'visibility' => ['request_path' => ['id' => 'request_path', 'pages' => '/transparency', 'negate' => FALSE]],
  ])->save();
  print "placed transparency dashboard\n";
}

// The public copy is static HTML with no login page, so a "Log in" link would 404.
if ($account = Block::load('c9_senate_account_menu')) {
  $account->disable()->save();
  print "hid account menu\n";
}

$config = \Drupal::configFactory();
$config->getEditable('c9_senate.settings')->set('logo.use_default', FALSE)->set('logo.path', '')->save();
// kn: aggregated CSS returned 400 under DDEV on this Mac; re-enable for real hosting.
$config->getEditable('system.performance')->set('css.preprocess', FALSE)->set('js.preprocess', FALSE)->save();
print "site settings applied\n";
