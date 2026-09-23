# C9 Funding Guide — Whimsy Spec (restrained)

Ground rules applied throughout: nothing playful inside the transparency table, budget
figures, or policy text. Gold accents only per BRAND.md (never gold text on white).
All motion wrapped in `@media (prefers-reduced-motion: no-preference)`. Plain HTML/CSS
plus tiny inline SVG; no JS. One slug touch max per page. Voice = "knowledgeable peer."

**Must-ships: #1, #3, #5.**

---

## 1. Form confirmation page — the moment that matters ⭐ MUST-SHIP
**Where:** Webform submission confirmation (Drupal webform confirmation message).
**What:** A `--c9-blue-light` card with a small inline-SVG globe (the C9 theme, not the logo —
a simple 24px circle with two meridian arcs in `--c9-teal`) and this copy:

> **Got it — your request is in.**
> You'll hear back after the next Senate meeting (Thursdays, 6–7:30 p.m., SocSci 1 rm 261).
> Want faster answers? Come present in person — it genuinely helps your case.
> Questions in the meantime? Email the VP of Finance.

**Effort:** S.
**Why it's safe:** Warmth lives in the copy and a themed icon; the process facts (when,
where, who) are the payload. No jokes near money.

## 2. Deadline banner pulse-once
**Where:** The `--c9-gold-tint` deadline banner on Home and How-to-Request pages.
**What:** On page load, the banner's left gold border (4px, `--c9-gold`) grows from 0 to
full height over 400ms, once. `animation: none` under reduced motion.
**Effort:** S.
**Why it's safe:** Draws the eye to a real deadline exactly once; no loop, no bounce.

## 3. 404 page ⭐ MUST-SHIP
**Where:** Custom 404 (Drupal system page / Tome static export).
**What:** Centered on `--c9-gray-100`. Inline SVG: a small yellow banana slug (simple
blob shape, `--c9-gold` body with `--c9-blue` eye-stalks, ~80px wide) next to the headline.

> **This page moved slower than a banana slug.**
> Actually, it doesn't exist. Here's where you probably wanted to go:
> [Funding Transparency] [Request Funding] [Funding Guidelines] [Home]

**Effort:** M (the slug SVG is the work).
**Why it's safe:** 404 is the one page with zero financial content — the sanctioned spot
for the mascot. Links do the real job. This is the page's single slug touch.

## 4. Empty transparency table state
**Where:** Views "no results" text on the Funding Transparency page (e.g., a quarter with
no awards yet).
**What:** Plain text in `--c9-gray-700`, no icon:

> **No awards yet this quarter.** Every funded request will show up here — organization,
> amount, and purpose. Yours could be first: [Request funding].

**Effort:** S.
**Why it's safe:** It's outside the table proper (the table is empty), states exactly what
the page promises, and turns absence into a call to action. Neutral tone near money.

## 5. "Funded" badge + status chips ⭐ MUST-SHIP
**Where:** Transparency page status column and any award callouts.
**What:** Small pill chips: Awarded = `--c9-green` dot + text; Denied = `--c9-red` dot + text;
gold left-border highlight (`--c9-gold`, 3px) on the single most recent award row is the only
flourish. On hover (pointer devices), chip background tints 6% — no motion.
**Effort:** S.
**Why it's safe:** This is BRAND.md's own "green/red delta chip" rule executed with one
quiet gold accent; it aids scanning rather than decorating. Not inside cell text.

## 6. Footer easter egg — the globe spins
**Where:** Site footer, next to the "Built by the College Nine Senate" line.
**What:** A 20px inline-SVG globe. On hover/focus: one full 360° rotation over 1.2s,
`transform: rotate(360deg)` with transition; none under reduced motion. `title` tooltip:
"International & Global Perspectives — that's us."
**Effort:** S.
**Why it's safe:** Discovery-only, invisible unless sought, reinforces the college theme,
touches no content.

## 7. Section illustrations — three-step "how to request" strip
**Where:** How-to-Request page header area.
**What:** Three inline SVG line icons in `--c9-blue` with one `--c9-gold` accent stroke each,
~48px: (1) pencil-on-form, (2) speech bubble over a table ("present Thursday"), (3) envelope
with a check mark ("decision by email"). Captioned with the real steps. Static.
**Effort:** M.
**Why it's safe:** Illustration as wayfinding — it compresses the process into a glance,
matches the two-color system, and contains no numbers.

## 8. Link + button micro-hover
**Where:** Site-wide primary buttons and nav links.
**What:** Buttons: background `--c9-blue` → `--c9-blue-dark` plus a 2px `--c9-gold`
underline sliding in from left (`background-size` trick, 200ms). Links: `--c9-teal` with
underline thickening on hover. No transform, no shadow bloom.
**Effort:** S.
**Why it's safe:** Standard affordance feedback in-palette; reads as polish, not play.

## 9. Guidelines checkbox microcopy
**Where:** The required "I have read the guidelines" checkbox on the request form.
**What:** Label: "I've actually read the [Funding Guidelines] (they're short, promise)."
Validation error if unchecked: "One sec — the guidelines are required reading. They take
about two minutes."
**Effort:** S.
**Why it's safe:** Honest, warm, and it increases the odds people really read the policy —
whimsy in service of compliance. The policy text itself stays untouched.

## 10. FAQ empty-feeling closer
**Where:** Bottom of the FAQ page.
**What:** Final "question":

> **My question isn't here.**
> That's what the VP of Finance is for. Email us — real question, real person, usually
> within two days.

**Effort:** S.
**Why it's safe:** Names the human (BRAND.md voice rule) and closes the dead-end every
FAQ page has. Pure copy.

## 11. Static-export form notice (GitHub Pages demo)
**Where:** The disabled form page on the Tome static export.
**What:** `--c9-blue-light` callout: "This is the read-only demo — the live form runs on
the Drupal site. Watch it work: [demo video]." Small inline-SVG play-triangle in `--c9-teal`.
**Effort:** S.
**Why it's safe:** Turns a broken-feeling limitation into a guided path; honest about
what's demo vs. live, which protects trust.

## 12. Print stylesheet sign-off
**Where:** `@media print` for the Guidelines page / PDF-adjacent output.
**What:** A print-only footer line in `--c9-gray-700`: "Printed from the College Nine
Senate funding site — figures current as of the print date. The web page is the source
of truth."
**Effort:** S.
**Why it's safe:** Feels considered rather than cute, and actively protects the money
figures from going stale on paper.

---

### Implementation notes
- Wrap every animation: `@media (prefers-reduced-motion: no-preference) { ... }`.
- Slug (idea 3) and globes (ideas 1, 6) are separate SVGs — never on the same page twice.
- All copy above is final-draft voice; tighten, don't jokey-fy, when editing.
