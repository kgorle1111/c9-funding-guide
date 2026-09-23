# c9_senate — Olivero sub-theme

## Where the files go

Copy this whole `c9_senate/` folder into your Drupal project at:

```
web/themes/custom/c9_senate/
├── c9_senate.info.yml
├── c9_senate.libraries.yml
├── css/
│   ├── tokens.css   ← COPY IN from /design/tokens.css (not in this skeleton)
│   └── main.css
└── templates/       ← create when you override a Twig template (Phase 5)
```

`web/themes/custom/` may not exist yet — create it. `contrib` is for downloaded themes,
`custom` is for yours; Drupal scans both.

Don't forget: `cp <repo>/design/tokens.css web/themes/custom/c9_senate/css/tokens.css`.
The libraries.yml loads tokens.css before main.css, so main.css can use its variables
directly — no `@import` needed (and Drupal's CSS aggregation prefers separate files over
@import anyway).

## Enable it

UI: Appearance (`/admin/appearance`) → find "C9 Senate" under Uninstalled themes →
**Install and set as default**.

Or drush:
```bash
ddev drush theme:enable c9_senate
ddev drush config:set system.theme default c9_senate -y
ddev drush cr
```

Keep Olivero installed — a sub-theme needs its base theme present (it inherits Olivero's
templates, CSS, and JS; your files layer on top).

## How sub-theming works (interview version)

- `base theme: olivero` in the info.yml makes Drupal fall back to Olivero for anything
  c9_senate doesn't provide: Twig templates, libraries, settings.
- **Regions are the one thing NOT inherited** — that's why the info.yml re-declares
  Olivero's region list. If block placement looks wrong, diff the regions against
  `web/core/themes/olivero/olivero.info.yml`.
- To override a template (Phase 5 requires editing at least one): copy it from
  `web/core/themes/olivero/templates/...` into `c9_senate/templates/`, keep the filename,
  edit, `ddev drush cr`. Drupal picks the sub-theme copy automatically by filename.
  To find which template a piece of the page uses, enable Twig debugging
  (`web/sites/default/services.yml` → `twig.config: debug: true`, then `ddev drush cr`) and
  read the HTML comments Drupal adds around every template.
- Never edit Olivero itself — core updates would wipe it.

## Gotcha

After ANY change to .info.yml, .libraries.yml, or adding a template file:
`ddev drush cr`. Plain CSS edits usually show up with a hard browser refresh, but when in
doubt, rebuild.
