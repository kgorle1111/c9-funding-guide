# C9 Funding Guide — Build Plan

**Goal:** The official College Nine Senate funding site, built in Drupal 11 by the VP of Finance.
Replaces the Google Site funding page + the retired Google Form. Doubles as the portfolio
project for the UCSC Admissions Web Designer application (deadline **Oct 5, 2026**).

**Delivers both real VPF mandates:**
1. Transparency system — anyone on eboard (or any student) opens one page and sees remaining
   funds for the year and what has been spent, org by org.
2. New funding request form — with the funding policies linked at the top.

**Demo formats:** narrated video of the Drupal admin workflow (via `/narrate`) + static export
(Tome) published to GitHub Pages. Public transparency page shows **sample data** with a visible
"sample figures pending board approval" note until the board signs off on real numbers.

**Budget:** 10–15 hours. $0 cost (all local/free tools).

---

## Phase 0 — Environment (≈1.5h)
- [ ] `brew install ddev/ddev/ddev` (needs Docker/OrbStack running)
- [ ] In this repo: `ddev config --project-type=drupal11 --docroot=web`
- [ ] `ddev start && ddev composer create drupal/recommended-project && ddev composer require drush/drush`
- [ ] `ddev drush site:install --account-name=admin -y && ddev launch`
- [ ] Commit: composer.json/lock, .ddev/config.yaml, .gitignore (already written)
- Checkpoint: fresh Drupal 11 loads at the ddev URL.

## Phase 1 — Content model (≈2h)  *(the Drupal-specific skill)*
- [ ] Content type **Funding Award**: fields = Organization (text), Quarter (list: Fall/Winter/Spring),
      Academic year (text), Amount awarded (decimal), Event/purpose (text), Status (list: Awarded/Denied).
- [ ] Content type **Basic page** (built-in) for: Home, Funding Guidelines, How to Request, FAQ, Executive Board.
- [ ] Enter ~15 sample Funding Award nodes (plausible fake orgs/amounts; keep a private branch/db with real data for the Senate rollout).
- Checkpoint: awards visible as individual nodes.

## Phase 2 — Transparency dashboard (≈2h)  *(VPF mandate #1)*
- [ ] Enable **Views**. Build "Funding Transparency" page:
      table of awards, grouped by quarter, with aggregate SUM per quarter and year total.
- [ ] Custom block at top: annual budget, total spent, **remaining funds** (Views aggregation; if the
      subtraction fights you, a 10-line Twig block does it — note which path you took for the interview).
- [ ] Add the "sample figures pending board approval" notice block.
- Checkpoint: one page answers "how much is left and where did it go."

## Phase 3 — Funding request form (≈1.5h)  *(VPF mandate #2)*
- [ ] `ddev composer require drupal/webform` + enable.
- [ ] Rebuild the retired Google Form as a Webform: org name, contact, C9 affiliate?, event, date,
      amount requested, justification-if-over-$200, file upload for budget breakdown.
- [ ] Policies **linked at the top of the form** + required "I have read the guidelines" checkbox.
- [ ] Email handler → c9senate@ucsc.edu (test with ddev's Mailpit; don't wire real email until board rollout).
- Checkpoint: submit a test request end-to-end, see it in Results + Mailpit.

## Phase 4 — Content & policies (≈1.5h)
- [ ] Guidelines page: full policy list including the auto-denial rules from the decision-email
      templates doc ($200 outside-org limit, no food costs, travel needs 25% C9 attendees,
      C9 affiliate participation, no costumes). Proofread — this page is the writing sample.
- [ ] How-to-request page: steps, Thursday 6–7:30pm SocSci 1 rm 261 presentations, response timeline.
- [ ] **PDF**: one-page "Funding Guidelines" export, attached as a download.
- [ ] **Video embed**: any relevant YouTube (e.g. a C9 event recap) on the home page.
- Checkpoint: every listed Admissions job duty now exists on the site (pages, blocks, form, PDF, video).

## Phase 5 — Photos & theme (≈2.5h)
- [ ] 6–10 photos from C9 events → **Photopea**: choose, crop, resize, export web-optimized. Record this step.
- [ ] Sub-theme of Olivero: C9 branding, hand-written CSS (colors, typography, spacing),
      edit at least one Twig template's HTML. Check color contrast (accessibility).
- Checkpoint: site looks like College Nine, not stock Drupal; screenshots read as professional.

## Phase 6 — Static export → GitHub Pages (≈2h)
- [ ] `ddev composer require drupal/tome` → `ddev drush tome:static` → static HTML in `/html`.
- [ ] Push to `gh-pages` branch (or /docs) of the GitHub repo; enable Pages.
- [ ] Static form can't submit — swap the form page's submit for a clearly labeled
      "live form runs on the Drupal site — demo video below" note, or link the video.
- Checkpoint: public URL loads, all pages browsable on phone + desktop.

## Phase 7 — Demo video + application (≈2h)
- [ ] `/narrate` a 3–4 min video: assignment framing → Photopea crop → edit a node → place a block
      → publish → submit a funding request → transparency page updates → 30s of the sub-theme CSS in the editor.
- [ ] Update resume "Web & Design Projects" section to match what was actually built; add the Pages URL.
- [ ] Message Jennifer via "Ask Jennifer": two lines + video link + Pages link.
- [ ] Separate track (not for the application): present site to Senate board for adoption + real-data approval.

## Explicitly out of scope
- Real email delivery, real budget figures on the public site (board approval first), user accounts
  for eboard, hosting the live Drupal anywhere public, the Slate-style dedup script (add only if
  hours remain after Phase 7 — it's the 15%+ stretch, not the core).

## Risks
- DDEV/Docker setup friction (first-time). Mitigation: Phase 0 is isolated; if >2h, fall back to `ddev`'s Drupal CMS quick-start docs.
- Views aggregation for "remaining funds" is the one fiddly bit — timebox 45 min, then use the Twig block.
- Tome export quirks with webform pages — known pattern; the Phase 6 note handles it.
