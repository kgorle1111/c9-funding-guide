#!/usr/bin/env bash
# Export the running site as static HTML into ./html, ready for GitHub Pages.
# Run from the repo root:  ./drupal/scripts/export_static.sh [path-prefix]
# The prefix defaults to /c9-funding-guide (GitHub project pages live under /<repo>/).
set -euo pipefail
export PREFIX="${1:-/c9-funding-guide}"

URL=$(ddev describe -j | python3 -c "import sys, json; print(json.load(sys.stdin)['raw']['primary_url'])")
rm -rf html
ddev drush tome:static --uri="$URL" -y

# Drupal writes root-relative links (/faq); under a project page they need the prefix.
find html \( -name '*.html' -o -name '*.css' -o -name '*.js' \) -print0 \
  | xargs -0 perl -pi -e 's{(href|src|action)="/(?!/)}{$1="$ENV{PREFIX}/}g; s{url\((["\x27]?)/(?!/)}{url($1$ENV{PREFIX}/}g'

# Static hosting can't receive a form post, so turn submit off and say why.
FORM=html/form/funding-request/index.html
perl -0pi -e 's{(<form[^>]*webform-submission-funding-request[^>]*>)}{$1<div class="c9-callout c9-callout--warning"><strong>Demo copy:</strong> this is a static snapshot of the site, so the form can\x27t be submitted here. On the full Drupal site, each request is saved and emailed to the Senate.</div>}; s{(<input[^>]*webform-button--submit[^>]*?)(\s*/?>)}{$1 disabled$2}' "$FORM"
grep -q 'Demo copy' "$FORM" || { echo "form notice was not inserted: check the form markup" >&2; exit 1; }

touch html/.nojekyll
echo "Exported to ./html"
