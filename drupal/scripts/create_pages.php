<?php

/**
 * Create the site's Basic pages from drupal/scripts/pages.json, with path
 * aliases and main-menu links; sets the front page. Idempotent by alias.
 * Run: ddev drush php:script drupal/scripts/create_pages.php
 */

use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\node\Entity\Node;
use Drupal\path_alias\Entity\PathAlias;

$pages = json_decode(file_get_contents(DRUPAL_ROOT . '/../drupal/scripts/pages.json'), TRUE);

// stem => [alias, menu label, menu weight] (transparency-intro becomes the intro of the /transparency page)
$map = [
  'home' => ['/home', 'Home', 0],
  'how-to-request' => ['/how-to-request', 'How to Request', 1],
  'funding-guidelines' => ['/funding-guidelines', 'Guidelines', 2],
  'faq' => ['/faq', 'FAQ', 3],
  'transparency-intro' => ['/transparency', 'Transparency', 4],
];

$alias_storage = \Drupal::entityTypeManager()->getStorage('path_alias');
foreach ($map as $stem => [$alias, $label, $weight]) {
  $existing = $alias_storage->loadByProperties(['alias' => $alias]);
  if ($existing) {
    print "exists  $alias\n";
    continue;
  }
  $node = Node::create([
    'type' => 'page',
    'title' => $pages[$stem]['title'],
    'body' => ['value' => $pages[$stem]['body'], 'format' => 'full_html'],
    'status' => 1,
  ]);
  $node->save();
  PathAlias::create(['path' => '/node/' . $node->id(), 'alias' => $alias])->save();
  MenuLinkContent::create([
    'title' => $label,
    'link' => ['uri' => 'internal:/node/' . $node->id()],
    'menu_name' => 'main',
    'weight' => $weight,
  ])->save();
  if ($stem === 'home') {
    \Drupal::configFactory()->getEditable('system.site')->set('page.front', '/node/' . $node->id())->save();
  }
  print "created $alias (node {$node->id()})\n";
}
print "done\n";
