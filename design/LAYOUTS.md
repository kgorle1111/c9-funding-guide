# College Nine Senate Funding — Page Layouts

Buildable specs for the three key pages. Everything below uses DESIGN-SYSTEM.md components
(`.c9-container`, stat card, table, callout, form, buttons, header) plus one small extra-CSS
budget per page, tallied at the end (total ≈ 40 lines, under the 50-line cap).

All pages sit inside Olivero's header/footer regions. Content column = `.c9-container`
(`--container-max` 1080px, `--gutter` 16px phone / `--gutter-wide` 32px ≥768px).

---

## Mobile nav (all pages)

No custom work. Olivero's hamburger, focus trapping, and sticky header are reused as-is;
the sub-theme only recolors: header band `background: var(--color-primary)`, 4px
`--color-accent` bottom stripe, white menu links with gold `text-decoration-color` on
hover/active (per DESIGN-SYSTEM §6). The globe logo (SVG, min 32px tall) sits top-left and
links home. At 375px Olivero collapses the menu behind its own button — verified behavior,
zero CSS from us beyond the recolor.

---

## 1. HOME

### Wireframe (desktop ≥768px)

```
┌──────────────────────────────────────────────────────────────┐
│ HEADER (Olivero, --color-primary band, gold 4px stripe)      │
├──────────────────────────────────────────────────────────────┤
│ HERO  (full-bleed band, --color-primary bg)                  │
│  ┌────────────────────────────┐  ┌────────────────────────┐  │
│  │ H1: College Nine Senate    │  │  [IMG-H1: hero photo]  │  │
│  │     Funding                │  │  students at C9 event  │  │
│  │ Lead: "Got an event…       │  │                        │  │
│  │  That's what we're here    │  └────────────────────────┘  │
│  │  for."                     │                              │
│  │ [ Request Funding ]  ←primary CTA (gold-keel treatment)   │
│  │ [ Read the guidelines ] ←secondary                        │
│  └────────────────────────────┘                              │
├──────────────────────────────────────────────────────────────┤
│ HOW IT WORKS — 4-step strip                                  │
│  ① Submit the   ② We reach out  ③ Present      ④ Decision    │
│    form           in 1–2 days     3–5 min at     by email,   │
│                                   a meeting      2–3 weeks   │
├──────────────────────────────────────────────────────────────┤
│ TWO-COLUMN ROW                                               │
│  ┌ Before you apply ────────┐  ┌ When we meet ───────────┐   │
│  │ • guidelines link        │  │ .c9-callout             │   │
│  │ • step-by-step guide     │  │ Thursdays 6:00–7:30pm   │   │
│  │ • transparency page      │  │ Social Sciences 1, R261 │   │
│  └──────────────────────────┘  │ Everyone is welcome.    │   │
│                                └─────────────────────────┘   │
├──────────────────────────────────────────────────────────────┤
│ QUESTIONS? band — email c9senate@ucsc.edu (link, --c9-teal   │
│  NOT used: link color token is --color-link = blue)          │
├──────────────────────────────────────────────────────────────┤
│ FOOTER (Olivero, --c9-blue-dark, UCSC wordmark above globe)  │
└──────────────────────────────────────────────────────────────┘
```

### Component + token mapping

| Region | Component | Tokens |
|---|---|---|
| Hero band | full-bleed `<section>`; inner `.c9-container` | bg `--color-primary`; text `--color-text-inverse`; H1 `--text-3xl` `--weight-bold` `--leading-tight`; lead `--text-lg` `--leading-body`; padding-block `--space-12` |
| Hero eyebrow (optional "College Nine Senate") | plain `<p>` | `--color-accent` on blue (7.2:1 AAA), `--text-sm`, uppercase, letter-spacing 0.06em — same recipe as `.c9-stat-card__label` |
| Primary CTA "Request Funding" | `.c9-btn` on a blue surface → **inverted primary**: `background: var(--c9-white); color: var(--color-primary)` (11.3:1). Extra CSS line item A. | radius `--radius-sm`, min-height 44px, focus `--focus-ring` |
| Secondary CTA "Read the guidelines" | `.c9-btn--secondary` variant on blue: transparent bg, `border-color: var(--c9-white)`, white text. Extra CSS line item A. | — |
| 4-step strip | ordered list, flex row, `gap: var(--space-4)`. Extra CSS line item B. | step number chip: `--color-accent` bg + `--color-text` text (11.1:1); step title `--weight-bold` `--color-primary`; body `--text-sm` `--color-text-muted`; section bg `--color-surface` |
| Before you apply | plain prose + links | links `--color-link`; list spacing `--space-2` |
| When we meet | `.c9-callout` (default blue-tint variant) | bg `--color-surface-brand`, border-left `--color-primary`, title `.c9-callout__title` |
| Questions band | `.c9-callout--warning` OR plain paragraph — use plain paragraph; gold banner is reserved for deadlines per BRAND Do/Don't | body text + `--color-link` mailto |

### Responsive @375px

- Hero: single column; photo drops **below** the text (order swap via flex column), CTA
  buttons stack full-width (`width: 100%` inside the stack), tap targets stay ≥44px.
- 4-step strip: flex wraps to a vertical list — one step per row, number chip left of text.
- Two-column row: stacks (callout after list). No horizontal scroll anywhere.

### Image slots

| Slot | Content | Crop (px, 2x-ready) | Rendered |
|---|---|---|---|
| IMG-H1 hero | candid photo, students at a C9 event, warm daylight | **1600 × 1000** (16:10) | ~480px wide desktop; full-width @375 (375×234 display) |
| Globe logo (header) | existing SVG | vector | 32–40px tall |

Photopea: export IMG-H1 as JPG quality ~75, target <180 KB. Keep faces off the left third
on the desktop crop (text column overlaps visually on mid widths).

---

## 2. FUNDING TRANSPARENCY (the money page)

### Wireframe (desktop)

```
┌──────────────────────────────────────────────────────────────┐
│ HEADER                                                       │
├──────────────────────────────────────────────────────────────┤
│ H1: Funding Transparency                                     │
│ Lead: "Your student fees fund this budget, so you deserve    │
│  to see where every dollar goes…"                            │
├──────────────────────────────────────────────────────────────┤
│ ⚠ SAMPLE-DATA NOTICE (.c9-callout--warning, gold tint)       │
│  "Figures shown are sample data pending Senate board         │
│   approval." — ABOVE the stat cards so no screenshot of the  │
│   numbers exists without it.                                 │
├──────────────────────────────────────────────────────────────┤
│ STAT CARD ROW                                                │
│  ┌ small ─────┐ ┌ small ─────┐ ┌═ HERO CARD ══════════════┐  │
│  │ ANNUAL     │ │ TOTAL      │ ║ REMAINING FUNDS (gold)   ║  │
│  │ BUDGET     │ │ SPENT      │ ║   $XX,XXX  ←--text-stat  ║  │
│  │ $XX,XXX    │ │ $X,XXX     │ ║ as of <date>, Quarter X  ║  │
│  └────────────┘ └────────────┘ ╚══ 6px gold keel ═════════╝  │
│  (small variant: --color-surface bg)   (blue .c9-stat-card)  │
├──────────────────────────────────────────────────────────────┤
│ "What you'll find here" + "Why we publish this" prose        │
├──────────────────────────────────────────────────────────────┤
│ H2: Awards by organization                                   │
│ ┌ .c9-table-wrap ────────────────────────────────────────┐   │
│ │ caption │ thead: Org | Purpose | Date | Amount | Status│   │
│ │ ▓ Fall Quarter (c9-table__quarter row, blue tint) ▓    │   │
│ │  Org A   | Event…  | 10/12 |   $450.00 | [Awarded]     │   │
│ │  Org B   | Supplies| 10/26 |   $200.00 | [Awarded]     │   │
│ │  Fall total (c9-table__sum) …………………     $650.00        │   │
│ │ ▓ Winter Quarter ▓  …repeat per quarter…               │   │
│ └────────────────────────────────────────────────────────┘   │
├──────────────────────────────────────────────────────────────┤
│ "Spot something off? Email c9senate@ucsc.edu"                │
│ FOOTER                                                       │
└──────────────────────────────────────────────────────────────┘
```

### Component + token mapping

| Region | Component | Tokens |
|---|---|---|
| Sample-data notice | `.c9-callout--warning` | bg `--color-warn-bg` (gold tint), border-left `--color-accent`, title `--color-primary`, body `--color-text`. Placement rule: directly under the lead paragraph, before any number. |
| Hero stat card (REMAINING) | `.c9-stat-card` verbatim from DESIGN-SYSTEM §1 | label gold-on-blue, value `--text-stat` (clamp 2.5–4rem) `tabular-nums`, meta 0.85 opacity, `--shadow-md`, 6px `--color-accent` keel |
| Small cards (budget / spent) | small stat variant per §1: `--color-surface` bg, `--color-text` label, value in `--color-primary` at `--text-2xl` `--weight-bold` `tabular-nums`. Extra CSS line item C (the row + small variant). | radius `--radius-md`, `--shadow-sm`, padding `--space-6` |
| Card row | flex, `gap: var(--space-4)`; hero card gets `flex: 1.4` so REMAINING is visibly dominant | — |
| Awards table | `.c9-table` inside `.c9-table-wrap`, `<tbody>` per quarter, `.c9-table__quarter` group rows, `.c9-table__sum` totals — exactly the §2 spec | thead `--color-primary`/inverse; quarter rows `--color-surface-brand` + `--color-primary`; amounts `td.is-amount` right-aligned tabular-nums; sums 2px `--color-primary` top border |
| Status badges | `.c9-badge--awarded/pending/denied` | success/warn/danger text + tint pairs (all AA); text names the status, never color-only |

Visual hierarchy intent: REMAINING is the only element on the page using `--text-stat` —
at any width it is the biggest number on screen, which is exactly what the screenshot shows.

### Responsive @375px

- Sample-data callout: full width, still first.
- Stat row: wraps to a **column** below 768px with REMAINING (hero card) **first** —
  `order: -1` on the hero card in the column stack (1 extra CSS line, in item C).
  `--text-stat` clamp lands ≈2.5rem, no overflow.
- Table: `.c9-table-wrap` is the page's only horizontal scroller. Column order puts
  Org + Amount within the first viewport-width so the scroll is optional for the gist.
- Nothing else scrolls horizontally at 375px (design-system rule).

### Image slots

None required — this page is numbers-first by design. Optional: a small meeting photo at
the bottom near the contact line, **800 × 500** (16:10), rendered ≤400px wide. Skip it for
v1; the table is the visual.

---

## 3. REQUEST FORM page

### Wireframe (desktop)

```
┌──────────────────────────────────────────────────────────────┐
│ HEADER                                                       │
├──────────────────────────────────────────────────────────────┤
│ H1: Request Funding                                          │
│ Lead: "Requesting funding takes one form and one short       │
│  presentation."                                              │
├──────────────────────────────────────────────────────────────┤
│ POLICY CALLOUTS (top, before the form — read-then-fill)      │
│ ┌ .c9-callout ───────────────────────────────────────────┐   │
│ │ Before you start                                       │   │
│ │ • Skim the funding guidelines — some costs (like food) │   │
│ │   can't be funded.                                     │   │
│ │ • Requests are reviewed in order — submit early.       │   │
│ └────────────────────────────────────────────────────────┘   │
│ ┌ .c9-callout--warning (gold tint — it's a hard limit) ──┐   │
│ │ Request limit                                          │   │
│ │ Orgs outside College Nine: up to $200. Above that      │   │
│ │ needs written justification or it's returned.          │   │
│ └────────────────────────────────────────────────────────┘   │
├──────────────────────────────────────────────────────────────┤
│ WEBFORM (.c9-form wrapping Drupal Webform markup)            │
│  Name*            [____________________]                     │
│  UCSC email*      [____________________]                     │
│  Organization*    [____________________]                     │
│  Event / project* [____________________]                     │
│  Amount ($)*      [________]  ← description: "itemized       │
│  Itemized budget* [ textarea           ]   budget = faster   │
│  Event date       [ date               ]   decision"         │
│  ── trust block ──────────────────────────────────────────   │
│  ▸ What happens next: we email you in 1–2 days to schedule   │
│    your 3–5 min presentation (Thursdays 6–7:30pm, Soc Sci 1  │
│    Rm 261). Decision by email in 2–3 weeks.                  │
│  ▸ Questions? Email c9senate@ucsc.edu — we're students too.  │
│  [ Submit request ]  ←.c9-btn--primary                       │
├──────────────────────────────────────────────────────────────┤
│ FOOTER                                                       │
└──────────────────────────────────────────────────────────────┘
```

### Component + token mapping

| Region | Component | Tokens |
|---|---|---|
| "Before you start" | `.c9-callout` (blue) | `--color-surface-brand`, border `--color-primary` |
| "Request limit" | `.c9-callout--warning` | gold tint + `--color-accent` border — gold = deadline/limit attention per BRAND §5 |
| Form | Drupal Webform inside `.c9-form`; style `.form-item`, `.form-required`, `.description` per §4 — **no custom field markup** | inputs: `--color-border`, `--radius-sm`, `--text-base`, padding `--space-3`; labels `--weight-medium`; descriptions `--text-sm` `--color-text-muted`; item spacing `--space-6`; focus `--focus-ring`; errors `--color-danger` border + Drupal's inline text |
| Trust block (near submit) | plain `<div>` above the button: `--text-sm`, `--color-text-muted`, top border 1px `--color-border`, padding-block `--space-4`. Extra CSS line item D. | mailto link `--color-link` |
| Submit | `.c9-btn.c9-btn--primary` | `--color-primary` bg, inverse text, hover `--color-primary-hover`, 44px min |

Content sourced from how-to-request.md; the page repeats meeting time/place at the point of
commitment (trust cue) rather than making the user navigate back.

### Responsive @375px

- Single column throughout (form is already `width: 100%` per §4).
- Callouts stack full-width above the form — order preserved (policies first).
- Submit button `width: 100%` below 768px (1 line, in item D).
- Amount field stays a short input but full-width on phone (no special casing needed).

### Image slots

None in the form flow — imagery between a user and a submit button is friction. Optional
header-adjacent thumbnail of a Senate meeting for warmth: **640 × 400** (16:10), rendered
~320px, right-aligned desktop only, hidden ≤767px (`display: none`, in item D). Skip if
photos aren't ready; the page works without it.

---

## Extra-CSS budget (beyond DESIGN-SYSTEM components)

| Item | What | ~Lines |
|---|---|---|
| A | Hero band bg/padding + inverted/outline button variants on blue | 10 |
| B | 4-step strip flex + numbered chip | 10 |
| C | Stat-card row flex + small-card variant + mobile `order:-1` | 12 |
| D | Trust block styling + full-width mobile submit + optional img hide | 6 |
| **Total** | | **~38 / 50** |

Nothing here exceeds the cap. Explicitly not needed: no new colors, fonts, or components;
tables, callouts, badges, forms, buttons, stat card, and nav are all stock design-system.

Note: BRAND.md specifies Roboto/Roboto Condensed while tokens.css ships a system stack and
DESIGN-SYSTEM builds on the tokens. These layouts follow tokens.css (system stack). If
Roboto is wanted, that's a tokens.css change, not a layout change.
