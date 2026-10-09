#!/usr/bin/env bash
# Build the whole site from an empty DDEV project. Run from the repo root:
#   ddev start && ./drupal/scripts/setup.sh
# Safe to re-run except for the first step, which wipes the database.
set -euo pipefail

ddev composer install
ddev drush site:install standard --site-name="College Nine Senate Funding" -y
ddev drush en webform tome admin_toolbar c9_funding -y

for script in create_content_types create_funding_award_fields create_sample_awards create_pages create_webform; do
  ddev drush php:script "drupal/scripts/${script}.php"
done

ddev drush theme:enable c9_senate -y
ddev drush config:set system.theme default c9_senate -y
ddev drush php:script drupal/scripts/configure_site.php
ddev drush cr

echo
echo "Done. Log in with: ddev drush uli"
