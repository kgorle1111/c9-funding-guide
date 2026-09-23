# C9 Funding Site — Drupal 11 Implementation Guide

Companion to `../PLAN.md`. Assumes DDEV on macOS, Phase 0 already done (fresh Drupal 11
running, `ddev launch` opens the site, you can log in as admin). Every `ddev drush` /
`ddev composer` command runs from the repo root.

Targets: **Drupal 11.x, Webform 6.3.x, Tome 8.x-1.16, Admin Toolbar 3.x** (versions verified
2026-09-23; sources at bottom).

Two vocab items you'll be asked about in an interview:
- **Node** = one piece of content (one funding award, one page). **Content type** = the schema for a class of nodes.
- **Views** = Drupal's built-in query builder that turns "SELECT ... GROUP BY quarter" into a rendered page, configured in the UI, exportable as YAML config.

---

## 1. Install modules

```bash
ddev composer require 'drupal/webform:^6.3' 'drupal/admin_toolbar:^3.5' 'drupal/tome:^1.16'
ddev drush en webform webform_ui admin_toolbar admin_toolbar_tools -y
# Do NOT enable tome yet — enable tome_static only in Phase 6 (section 6).
ddev drush cr
```

What each does:
- **webform** — the form builder. `webform_ui` gives you the drag-and-drop form editor (without it you edit YAML).
- **admin_toolbar** (+ `admin_toolbar_tools`) — replaces the stock admin menu with hover dropdowns. Pure quality-of-life; install it first, everything below gets faster.
- **tome** — static site generator. Its `tome_static` submodule renders every route to plain HTML.

`ddev drush cr` = cache rebuild. Drupal caches aggressively (routes, plugins, Twig). **When
something you just changed doesn't show up, `ddev drush cr` before debugging anything else.**

Views and Field UI ship with core and are already enabled on a standard install. Verify:
`ddev drush pml | grep -E 'views|field_ui'` should show both as Enabled.

---

## 2. Content type: Funding Award (admin UI, click-by-click)

Do this in the UI, not code — content types + fields are **configuration entities**; Drupal
stores them as YAML you can export later (`ddev drush cex`), so UI-created config is still
"config in code" once exported. (That's the Drupal answer to "did you build this in the UI?")

1. **Structure → Content types → Add content type** (`/admin/structure/types/add`)
   - Name: `Funding Award`. Machine name auto-fills as `funding_award` — leave it.
   - Under "Display settings": uncheck *Display author and date information* (awards aren't blog posts).
   - Save.
2. You land on **Manage fields** for the new type. Delete the default **Body** field
   (Operations → Delete) — we use dedicated fields instead. The built-in **Title** field will
   hold a label like "Ballroom Dance Club — Fall 2026" (Views needs a link text anyway).
3. Add each field with **Create a new field**. For each: pick the type, set the label
   (machine name auto-fills as `field_...`), Save, keep defaults on the settings screens
   unless noted:

   | Label | Field type | Notes |
   |---|---|---|
   | Organization | Text (plain) | |
   | Quarter | List (text) | Allowed values, one per line: `fall\|Fall`, `winter\|Winter`, `spring\|Spring` (format is `key\|label`). Widget: Select list. Required. |
   | Academic year | Text (plain) | Free text like `2026–27`. (List would be "cleaner" but you'd edit config every year; text is the right tradeoff — say that in the interview.) |
   | Amount awarded | Number (decimal) | Precision 10, scale 2. Prefix `$` on the display settings later if you like. Required. |
   | Event/purpose | Text (plain, long) | |
   | Status | List (text) | Allowed values: `awarded\|Awarded`, `denied\|Denied`. Required. |

4. **Manage form display** tab: drag fields into a sensible entry order. **Manage display**
   tab: hide labels you don't want (e.g. set Title-adjacent labels to "Inline").
5. Create the ~15 sample nodes: **Content → Add content → Funding Award**.
   Faster: `ddev drush php` and a small loop with `Node::create([...])->save()` — but for a
   first-timer the UI is fine and you'll want the muscle memory for the demo video.

Checkpoint: `/admin/content` filtered by type Funding Award shows your nodes.

---

## 3. Views page: Funding Transparency

Goal: a table of awards grouped by quarter, with a SUM of amounts per quarter.

1. **Structure → Views → Add view** (`/admin/structure/views/add`)
   - View name: `Funding Transparency`.
   - Show: Content, of type: Funding Award, sorted by: Unsorted.
   - Check **Create a page**. Path: `funding-transparency`. Display format:
     **Table** of fields. Items to display: `0` (0 = all). Uncheck pager if offered.
   - Save and edit.
2. In the view edit screen, under **Fields**, click Add and add:
   `Organization`, `Amount awarded`, `Event/purpose`, `Status`, and `Quarter`.
   Remove the default `Title` field or keep it as the row label — your call.
3. **Filter criteria**: add `Status (= Awarded)` **only if** the transparency page should hide
   denials. PLAN.md implies showing both (transparency), so probably skip the filter and just
   show the Status column. Add a filter `Published = Yes` if not already there.
4. **Grouping by quarter**: under **Format → Table → Settings** … actually grouping lives on
   the *format*: click **Format: Table | Settings**, set **Grouping field Nr.1** = Quarter.
   Each quarter now renders as its own sub-table with a heading.
5. **SUM per quarter — the fiddly bit.** Views aggregation: in the right column under
   **Advanced**, set **Use aggregation: Yes**. Every field now gets an "Aggregation type"
   choice. This *changes the query* to GROUP BY — which fights with also showing individual
   rows. **Don't use it for the per-quarter subtotal.** Two sane options:
   - **Option A (recommended): keep the detail table un-aggregated and add a *second* view
     display** (Add → Attachment, attached to the page) that IS aggregated: fields = Quarter
     + Amount awarded with Aggregation type **Sum**, grouped/sorted by Quarter. Result: the
     detail table plus a compact "Fall: $X / Winter: $Y / Spring: $Z" summary table above or
     below it. Two displays, one view, no code.
   - **Option B:** skip per-quarter sums in Views entirely and let the custom block
     (section 4) show year totals only. Fine for MVP; A is a better interview story.
   - Also possible: theme the table footer in Twig — more code for less clarity; skip.
6. Add a **year total**: on the aggregated attachment display, remove the Quarter grouping
   in a third display, or simpler — the custom block in section 4 shows the year total anyway.
   Don't build the same number twice.
7. Save the view. Visit `/funding-transparency`.

Interview note: "Use aggregation" toggles the whole display's query into GROUP BY mode —
that's why the summary lives on a separate display of the same view instead of the detail table.

Checkpoint: page shows quarter-grouped detail rows + per-quarter sums.

---

## 4. Remaining funds: small custom block (recommended over Views)

**Decision: custom block with a computed value, not Views aggregation. Why:** "remaining =
fixed budget − SUM(amount)" needs a constant (the annual budget) and an arithmetic step.
Views can SUM but has no clean place to subtract from a constant — you end up abusing global
math fields or theming a Views footer. A ~40-line block plugin does one entity query and one
subtraction, and it's honest code you can explain line by line. PLAN.md already blesses this
path ("timebox 45 min, then use the Twig block").

Create a tiny custom module. Files under `web/modules/custom/c9_funding/`:

**`c9_funding.info.yml`**
```yaml
name: C9 Funding
type: module
description: 'Remaining-funds block for the transparency page.'
core_version_requirement: ^11
package: Custom
```

**`src/Plugin/Block/RemainingFundsBlock.php`**
```php
<?php

namespace Drupal\c9_funding\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

#[Block(
  id: 'c9_remaining_funds',
  admin_label: new TranslatableMarkup('C9 Remaining Funds'),
)]
class RemainingFundsBlock extends BlockBase {

  // ponytail: hardcoded budget + year; move to block config form when the
  // Senate needs to change it without a deploy.
  const ANNUAL_BUDGET = 15000.00;
  const ACADEMIC_YEAR = '2026-27';

  public function build(): array {
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $nids = $storage->getQuery()
      ->condition('type', 'funding_award')
      ->condition('status', 1)
      ->condition('field_status', 'awarded')
      ->condition('field_academic_year', self::ACADEMIC_YEAR)
      ->accessCheck(TRUE)
      ->execute();
    $spent = 0.0;
    foreach ($storage->loadMultiple($nids) as $node) {
      $spent += (float) $node->get('field_amount_awarded')->value;
    }
    return [
      '#theme' => 'c9_remaining_funds',
      '#budget' => self::ANNUAL_BUDGET,
      '#spent' => $spent,
      '#remaining' => self::ANNUAL_BUDGET - $spent,
      '#cache' => ['tags' => ['node_list:funding_award']],
    ];
  }

}
```

**`c9_funding.module`**
```php
<?php

/**
 * Implements hook_theme().
 */
function c9_funding_theme(): array {
  return [
    'c9_remaining_funds' => [
      'variables' => ['budget' => 0, 'spent' => 0, 'remaining' => 0],
    ],
  ];
}
```

**`templates/c9-remaining-funds.html.twig`**
```twig
<div class="c9-funds">
  <div class="c9-funds__item"><span>Annual budget</span> ${{ budget|number_format(2) }}</div>
  <div class="c9-funds__item"><span>Awarded to date</span> ${{ spent|number_format(2) }}</div>
  <div class="c9-funds__item c9-funds__item--remaining"><span>Remaining</span> ${{ remaining|number_format(2) }}</div>
</div>
```

Enable and place:
```bash
ddev drush en c9_funding -y && ddev drush cr
```
Then **Structure → Block layout**, pick your theme's tab, **Place block** in *Content* (or
*Highlighted*) region → find "C9 Remaining Funds" → in the visibility settings restrict to
**Pages: `/funding-transparency`**. Save.

Two things to be able to explain:
- `#cache => tags => node_list:funding_award` — the block's render cache is invalidated
  whenever any funding_award node is added/edited, so the number is never stale. Without it
  Drupal would happily cache the old total forever.
- Field machine names: mine assume `field_amount_awarded`, `field_status`,
  `field_academic_year`. **Check yours** at Structure → Content types → Funding Award →
  Manage fields and adjust the query if they differ.

The "sample figures pending board approval" notice: **Structure → Block layout → Add custom
block** (plain text, no code needed), place it above the funds block on the same page.

---

## 5. Funding request webform

1. **Structure → Webforms → Add webform** (`/admin/structure/webform/add`). Title:
   `Funding Request`.
2. In the Build tab, **Add element** for each (per PLAN.md Phase 3):
   - Organization name — Text field, required
   - Contact name — Text field, required
   - Contact email — Email, required
   - College Nine affiliate? — Radios (Yes/No), required
   - Event name — Text field, required
   - Event date — Date, required
   - Amount requested — Number (set min 0, step 0.01), required
   - Justification (if over $200) — Textarea. Optional polish: add a **Conditional** on this
     element (States tab): *Visible when Amount requested ≥ 200*. Nice interview moment; skip if fiddly.
   - Budget breakdown — File upload (limit to pdf, xlsx, csv; Webform stores uploads privately by default — keep that)
   - "I have read the funding guidelines" — Checkbox, **required**
3. **Policies link at the top**: Add element → **Advanced HTML/text** (or Basic HTML),
   drag it to the very top, content: a short line linking to `/funding-guidelines`
   (the Phase 4 page). The guidelines checkbox description can link there too.
4. **Email handler**: webform's **Settings → Emails/Handlers → Add email**.
   - To: `c9senate@ucsc.edu`. Leave From as default (site mail).
   - Body: default (includes all submitted values).
5. **Test via Mailpit** (DDEV routes all outbound mail to Mailpit automatically — nothing to configure):
   ```bash
   ddev launch --mailpit    # opens the Mailpit UI
   ```
   Submit the form at its URL (shown on the webform's View tab), then check:
   the submission appears under the webform's **Results** tab AND the email appears in Mailpit.
   No real mail leaves your machine — exactly what PLAN.md wants pre-rollout.

Checkpoint: end-to-end test submission visible in Results + Mailpit.

---

## 6. Export config (do this before Phase 6, and after any config change)

```bash
ddev drush cex -y   # writes all config (content type, fields, view, webform, blocks) to config/sync as YAML
git add config/ && git status
```
This is the "configuration belongs in code" step — the content type you clicked together in
section 2 is now versioned YAML. `ddev drush cim` replays it onto a fresh site.

---

## 7. Tome static export → GitHub Pages

```bash
ddev drush en tome_static -y
ddev drush tome:static --uri=https://<your-username>.github.io/<repo-name>
```
The `--uri` matters: Tome writes absolute-ish URLs based on it; without it links break on
Pages. Output lands in `html/` at the project root.

**Caveat you must handle: webforms do not submit on static hosting.** The exported form page
is inert HTML — its POST target (the Drupal route) doesn't exist on GitHub Pages. Per
PLAN.md: before exporting, edit the "How to Request" / form page copy so the static site
shows a clearly labeled note: *"The live request form runs on the Drupal site — see the demo
video"* (link the video). Simplest mechanics: create a Basic page at the same conceptual spot
for the static build, or just add the notice text above the form so it reads sanely even inert.

Publish (using the `/docs` folder route — simpler than a `gh-pages` branch):
```bash
rm -rf docs && cp -R html docs
git add docs && git commit -m "chore: static export"   # get approval per your git rules
git push
```
Then GitHub repo → **Settings → Pages → Deploy from a branch → main → /docs**. Wait ~1 min,
load the URL, click every nav link, check on a phone.

If any page 404s in the export, re-run `tome:static` after `ddev drush cr`; Tome only knows
routes Drupal knows. Known quirk: pages behind access control export as 403s — keep
everything public.

---

## 8. Enable the c9_senate theme

See `theme/README-THEME.md` in this directory. Short version: copy `theme/c9_senate/` to
`web/themes/custom/c9_senate/`, copy your `tokens.css` from `/design/` into its `css/`
folder, then:
```bash
ddev drush theme:enable c9_senate && ddev drush config:set system.theme default c9_senate -y && ddev drush cr
```

---

## Uncertainties / things I did not verify

- Exact UI wording drifts between Drupal 11 minor versions (e.g. "Create a new field" flow
  was redesigned in 10.2+ — you pick a category tile first, then the type). The paths
  (`/admin/structure/...`) are stable; trust those over button labels.
- Webform element names in the Add-element list ("Advanced HTML/text" vs "Basic HTML") —
  either works for the policies blurb.
- Field machine names in the block code — verify against your actual field list (section 4).
- Whether Views "Grouping field" + a separate aggregated attachment display behaves exactly
  as described in Webform/Views current minor — the pattern is standard, but budget the
  PLAN.md 45-minute timebox; the custom block already covers the headline numbers if the
  attachment fights you.

## Sources
- Webform 6.3.0, first stable D11 release; requires ≥ Drupal 10.3 / PHP 8.1: https://www.drupal.org/project/webform/releases/6.3.0 and https://www.thedroptimes.com/71361/webform-630-adds-stable-drupal-11-support
- Tome releases — D11 support since 8.x-1.13, latest 8.x-1.16: https://www.drupal.org/project/tome/releases ; static docs: https://tome.fyi/docs/technical/static/
- Olivero sub-theming (base theme key, regions not inherited): https://www.drupal.org/forum/support/theme-development/2024-08-26/subtheme-olivero and https://developpeur-drupal.com/en/article/update-create-drupal-10-olivero-sub-theme
- Admin Toolbar project page: https://www.drupal.org/project/admin_toolbar
