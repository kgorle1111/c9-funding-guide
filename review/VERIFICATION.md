# Pre-Demo Verification — C9 Funding Guide
Date: 2026-09-23 · Verifier: independent integration check (evidence-only)

## VERDICT: NEEDS FIXES

Blocking items before recording:
1. **Broken visible link on the homepage**: "See our transparency page" links to `/transparency-intro`, which returns **404 on BOTH hosts**. This is in the demo's opening page copy.
2. **Site logo 404 on every page**: header references `/themes/custom/c9_senate/logo.svg`; the file does not exist in the theme. `curl` → 404 on live and static. Every page renders a broken-image logo in the header.
3. **Duplicate "Home" in the main nav**: two Home menu items on every page (one → `node/16` `/home`, one → `<front>`). Visually obvious in a demo.
4. **Static export (localhost:8090) is stale**: contains none of the 4 photos (`grep -c c9-photo static/*.html` → 0 on all pages; photo files 404 on 8090). If the video shows 8090, re-export first. Live site is fine.

---

## Check 1: All 6 pages 200 + real content — PASS (live) / PASS with note (static)

```
for p in / /how-to-request /funding-guidelines /faq /transparency /form/funding-request
curl -o /dev/null -w "%{http_code}"
```
Live (c9-funding-guide.ddev.site): all six → **200**.
Static (localhost:8090): all six → **301 → 200** (trailing-slash redirect, e.g. `/faq -> 200 http://localhost:8090/faq/`). Harmless.

Content verified on both hosts: correct `<title>` per page (Home, How to Request Funding, Funding Guidelines, Frequently Asked Questions, Funding Transparency, Funding Request), full body copy present (see Check 8 extraction). Static page bodies match live except the missing photos (Check 3/4).

## Check 2: Transparency math — PASS

Recomputed from the Drupal DB:
```
ddev drush php:eval  # over all funding_award nodes (field_amount, field_status)
AWARDED=2345 DENIED=0 REMAINING=2655
```
15 nodes: 13 awarded, 2 denied (Anime & Manga Circle, Esports at Nine — both $0.00, so they cannot inflate the total; even if they carried amounts, the recompute excluded status=denied). Quarter subtotals recomputed by hand: Fall 200+175+120+200=695, Winter 350+150+200+90=790, Spring 180+200+240+110+130=860; 695+790+860=2,345.

Rendered page (both hosts): **Annual budget $5,000.00, Awarded so far $2,345.00, Remaining this year $2,655.00**, quarter totals $695.00/$790.00/$860.00. All match. 2,345 + 2,655 = 5,000. ✓

## Check 3: Photos — PASS (live) / FAIL (static)

Live, all 4 (`curl -w '%{http_code} %{content_type} %{size_download}'` on `/themes/custom/c9_senate/images/<name>`):
```
hero-tabling.jpg  200 image/jpeg 128112
senate-group.jpg  200 image/jpeg  71103
senate-table.jpg  200 image/jpeg 225381
tiedye-event.jpg  200 image/jpeg 113327
```
Alt text: all 4 `<img class="c9-photo">` tags have non-empty descriptive alt (e.g. "College Nine Senate members tabling outside the residence halls with a prize wheel"). ✓

Static host: all 4 photo URLs → **404** (`html/themes/custom/c9_senate/images/` does not exist), and the static pages contain **zero** `c9-photo` img tags — the Tome export predates the photo additions. Exactly the stale-export case flagged in the brief. Re-run the export before demoing 8090.

FAIL item on both hosts: `logo.svg` (referenced in the site-branding header, alt="Home") → **404 live and static**. Not one of the 4 photos, but a broken image on every page.

## Check 4: Internal links on localhost:8090 — FAIL (2 broken)

Extracted every `href` from the 6 static pages. Results:
- `/`, `/home`, `/how-to-request`, `/funding-guidelines`, `/faq`, `/transparency`, `/form/funding-request`, all CSS/font/favicon assets → 200. ✓
- **`/transparency-intro` → 404 on static AND live.** Source: homepage body, `<a href="/transparency-intro">transparency page</a>`. Broken link in visible copy — blocking.
- `/user/login` → 404 on static (200 on live). Expected export gap for the "Log in" secondary-menu link; only matters if the video clicks it. Consider hiding the Log in link for the demo anyway.
- `<head>` canonical/shortlink links point at `https://c9-funding-guide.ddev.site/...` and `node/16–20` — metadata only, not user-visible; cosmetic for a static export.

## Check 5: Webform fields — PASS

Live `/form/funding-request` markup contains:
- All fields: `org_name`, `contact_name`, `contact_email`, `c9_affiliate` (Yes/No), `event_name`, `event_date`, `amount`, `justification`, `budget_breakdown`, `guidelines_read`.
- Conditional justification textarea present with `data-drupal-states`: visible+required when `c9_affiliate = No` AND `amount > 200`. (Note: the two conditions are ANDed — matches the stated rule "outside orgs requesting more than $200".)
- Guidelines-read checkbox: `<input type="checkbox" name="guidelines_read" required>`. ✓
- Two callouts on top: `c9-callout` ("Before you start…") and `c9-callout--warning` ("Request limit…"). ✓
Static form page has the same markup (6/6 grep hits), but submission obviously only works on the live site — demo the form on ddev.

## Check 6: Placeholder/broken content — PASS

`grep -in 'lorem\|TODO\|example\.com\|href=""'` across all 12 downloaded pages: **no matches**.
Sample-data notice present on /transparency on both hosts: "Figures shown are sample data pending Senate board approval." ✓
Double-space scan of visible text: all apparent hits are artifacts of stripping inline `<strong>`/`<a>` tags; no literal double spaces in rendered copy.

## Check 7: Repo hygiene — PASS with one note

- `git status`: **one uncommitted change** — `.ddev/config.yaml` adds `additional_fqdns: [c9.localhost]`. No secrets in the diff; commit or discard before recording, but not blocking.
- `.gitignore` lines 39/47: `/html/` and `/photos-raw/` ignored; `git ls-files | grep -cE '^(html|photos-raw)/'` → 0 tracked. ✓
- `drupal/scripts/`: `create_funding_award_fields.php`, `create_pages.php`, `create_sample_awards.php`, `create_webform.php`, `pages.json` (4 PHP scripts + 1 data file — if a 5th script was expected, it isn't there). Grep for `password|secret|token|api key|Bearer`: **no matches**.
- Secret grep across all tracked non-vendor files: no matches.

## Check 8: Proofreading of visible text — PASS

Extracted the full visible text of all 6 live pages (tag-stripped). Read end to end: no spelling errors, consistent punctuation and dash usage, consistent facts across pages (1–2 days to schedule, 3–5 minute presentation, 2–3 weeks to decision, Thursdays 6:00–7:30pm, Social Sciences 1 Room 261, $200 outside-org limit, food never funded, 25% travel threshold). Copy is clean and internally consistent.

One structural nit (not grammar): the nav reads "Home / Home / How to Request / …" because of the duplicate menu item (Blocking item 3).

---

## Ranked fix list

1. Fix the homepage link `/transparency-intro` → `/transparency` (broken 404 in visible copy on both hosts).
2. Add `logo.svg` to `web/themes/custom/c9_senate/` (or point site branding at an existing asset) — currently a header 404 on every page.
3. Remove the duplicate "Home" menu item (two entries: node/16 and `<front>`).
4. Re-run the Tome/static export so localhost:8090 picks up the 4 photos (and re-verify), or record the demo against the live ddev site only.
5. Commit or revert the uncommitted `.ddev/config.yaml` change (`c9.localhost` FQDN).
6. Optional polish: hide the "Log in" secondary menu for anonymous users (404 on the static host; irrelevant to visitors).

After fixes 1–3 (and 4 if 8090 appears in the video): READY TO RECORD.
