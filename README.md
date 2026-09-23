# College Nine Senate Funding Site

**Demo project.** A Drupal 11 site for the College Nine Senate (UC Santa Cruz) funding process, built by the Senate's VP of Finance. All budget figures and organization names shown on the site are **sample data** pending Senate board approval; this is not yet the Senate's official published site.

**Live demo:** https://kgorle1111.github.io/c9-funding-guide/ (static export via Tome)

## What it does

- **Funding transparency dashboard** — annual budget, amount awarded, remaining funds, and every award grouped by quarter with subtotals, including denied requests and the reason.
- **Funding request webform** — policies linked at the top, and conditional logic: a written-justification field appears automatically when an outside organization requests more than the $200 limit.
- **Content pages** — how to request funding, guidelines, FAQ, all written for students and proofread.
- **Custom theme** — an Olivero sub-theme on UCSC's brand colors (blue `#003c6c`, gold `#fdc700`) with WCAG-checked contrast and a mobile-first layout.
- **Photos** — cropped, resized, and web-optimized from real College Nine events, with alt text.

## Stack

Drupal 11 on DDEV (Colima/Docker), Webform, Tome (static export), custom module `c9_funding` for the dashboard block, custom theme `c9_senate`.

## Repo tour

| Path | What |
|---|---|
| `web/modules/custom/c9_funding/` | Transparency dashboard block (cache-tagged so totals refresh on any award edit) |
| `web/themes/custom/c9_senate/` | Sub-theme: design tokens + component CSS |
| `drupal/scripts/` | Idempotent drush scripts: content model, sample awards, pages, webform |
| `design/` | Brand guide, design tokens, layout specs |
| `content/` | Page copy (markdown source of truth) |
| `slate-demo/` | Record consolidation/dedup demo (Python, stdlib, tested) |
| `docs/demo/` | Screenshot rig + narrated demo video |

## Run it locally

```bash
ddev start
ddev drush site:install standard -y
ddev composer install
ddev drush en webform tome admin_toolbar c9_funding -y
ddev drush php:script drupal/scripts/create_funding_award_fields.php
ddev drush php:script drupal/scripts/create_sample_awards.php
ddev drush php:script drupal/scripts/create_pages.php
ddev drush php:script drupal/scripts/create_webform.php
ddev drush theme:enable c9_senate -y && ddev drush config:set system.theme default c9_senate -y
```

Static export: `ddev drush tome:static` (the published gh-pages branch also rewrites absolute paths with the `/c9-funding-guide/` prefix for GitHub Pages).
