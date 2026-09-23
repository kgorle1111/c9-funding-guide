# College Nine Senate Funding — Design System

How to apply `tokens.css` in the Olivero sub-theme. Target: hand-buildable in ~2 hours, vanilla CSS, additive overrides only.

## Drupal / Olivero ground rules

- This is a **sub-theme of Olivero** (Drupal 11). Olivero already handles resets, base typography, responsive nav mechanics, and accessibility plumbing. **Do not re-reset.** Write component classes (`.c9-*` prefix) and let Olivero's base cascade underneath.
- Load order in `c9.libraries.yml`: `tokens.css` first, then one `components.css`. Two files total.
- Where Olivero's own variables exist (e.g. `--color--primary-*` in Olivero ≥ 10.1), you may map them in `:root` to our tokens instead of fighting specificity:
  ```css
  :root {
    --color--primary-50: var(--c9-blue);
    --color--primary-40: var(--c9-blue-dark);
  }
  ```
  This recolors Olivero's buttons, links, and header rail to UCSC Blue with zero overrides.
- Never use `--color-accent` (gold) for text on light backgrounds (1.6:1). Gold is a surface, border, or underline color, always paired with `--color-text` or sitting on `--color-primary`.

## Page shell

- Content column: `max-width: var(--container-max)` (1080px), centered, `padding-inline: var(--gutter)`.
- Breakpoints (match Olivero's):
  - **≤ 767px (incl. 375px phone):** single column, gutter 16px, tables switch to scroll wrapper (below), stat card number scales via `clamp()` automatically.
  - **≥ 768px:** gutter becomes `--gutter-wide` (32px); two-column layouts allowed.
  - **≥ 1080px:** column caps; whitespace grows outside.
- At 375px nothing should horizontally scroll except the table wrapper.

```css
.c9-container {
  max-width: var(--container-max);
  margin-inline: auto;
  padding-inline: var(--gutter);
}
@media (min-width: 768px) {
  .c9-container { padding-inline: var(--gutter-wide); }
}
```

## Components

### 1. Stat card — "remaining funds" hero

The single most-looked-at element. One card, centered, near top of the funding page.

```css
.c9-stat-card {
  background: var(--color-primary);
  color: var(--color-text-inverse);
  border-radius: var(--radius-md);
  padding: var(--space-8) var(--space-6);
  text-align: center;
  box-shadow: var(--shadow-md);
  border-bottom: 6px solid var(--color-accent); /* gold keel */
}
.c9-stat-card__label {
  font-size: var(--text-sm);
  font-weight: var(--weight-medium);
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--color-accent); /* gold on blue: 7.2:1, AAA */
}
.c9-stat-card__value {
  font-size: var(--text-stat);
  font-weight: var(--weight-bold);
  line-height: var(--leading-tight);
  font-variant-numeric: tabular-nums;
}
.c9-stat-card__meta { /* "as of <date>, Quarter X" */
  font-size: var(--text-sm);
  opacity: 0.85;
}
```

Optional row of 2–3 smaller stat cards (allocated / spent / remaining): a flex row with `gap: var(--space-4)`, wrapping to a column below 768px. Small variant uses `--color-surface` background with `--color-text` and a `--color-primary` value.

### 2. Data table — funding transparency

Structure: `<caption>`, `<thead>`, one `<tbody>` per quarter. Quarter heading is a full-width `<th colspan scope="rowgroup">` row; each quarter's last row is a sum row.

```css
.c9-table-wrap { overflow-x: auto; } /* the only horizontal scroller, saves 375px */
.c9-table {
  width: 100%;
  border-collapse: collapse;
  font-size: var(--text-sm);
}
.c9-table th, .c9-table td {
  padding: var(--space-3) var(--space-4);
  text-align: left;
  border-bottom: 1px solid var(--color-border);
}
.c9-table thead th {
  background: var(--color-primary);
  color: var(--color-text-inverse);
  font-weight: var(--weight-medium);
}
.c9-table .c9-table__quarter th { /* quarter grouping row */
  background: var(--color-surface-brand);
  color: var(--color-primary);
  font-weight: var(--weight-bold);
}
.c9-table .c9-table__sum td { /* per-quarter total */
  font-weight: var(--weight-bold);
  border-top: 2px solid var(--color-primary);
  background: var(--color-surface);
}
.c9-table td.is-amount {
  text-align: right;
  font-variant-numeric: tabular-nums;
}
```

Status badges in the table (Awarded / Pending / Denied) — text color + tint pairs from tokens, all AA:

```css
.c9-badge {
  display: inline-block;
  padding: 2px var(--space-2);
  border-radius: var(--radius-sm);
  font-weight: var(--weight-medium);
  font-size: var(--text-sm);
}
.c9-badge--awarded { color: var(--color-success); background: var(--color-success-bg); }
.c9-badge--pending { color: var(--color-warn);    background: var(--color-warn-bg); }
.c9-badge--denied  { color: var(--color-danger);  background: var(--color-danger-bg); }
```

Don't rely on color alone: the badge text itself says the status, which satisfies WCAG 1.4.1.

### 3. Policy callout box

For "must apply 2 weeks before event"-type rules.

```css
.c9-callout {
  background: var(--color-surface-brand);
  border-left: 4px solid var(--color-primary);
  border-radius: 0 var(--radius-md) var(--radius-md) 0;
  padding: var(--space-4) var(--space-6);
  margin-block: var(--space-6);
}
.c9-callout--warning { background: var(--color-warn-bg); border-left-color: var(--color-accent); }
.c9-callout__title { font-weight: var(--weight-bold); color: var(--color-primary); margin-bottom: var(--space-2); }
```

### 4. Form fields + required indicator

Drupal renders forms with its own markup (`.form-item`, `.form-required`); style those classes rather than inventing new ones so Webform/core forms pick it up automatically.

```css
.c9-form input[type="text"],
.c9-form input[type="email"],
.c9-form input[type="number"],
.c9-form select,
.c9-form textarea {
  width: 100%;
  padding: var(--space-3);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-sm);
  font-size: var(--text-base);
  background: var(--color-bg);
  color: var(--color-text);
}
.c9-form :is(input, select, textarea):focus-visible {
  outline: none;
  box-shadow: var(--focus-ring);
}
.c9-form label {
  display: block;
  font-weight: var(--weight-medium);
  margin-bottom: var(--space-1);
}
/* Drupal core already renders <span class="form-required"> — recolor it */
.form-required::after { color: var(--color-danger); } /* Olivero uses an asterisk/icon */
.c9-form .description { font-size: var(--text-sm); color: var(--color-text-muted); }
.c9-form .form-item { margin-block-end: var(--space-6); }
```

Error state: `border-color: var(--color-danger)` on `.error` inputs plus a `--color-danger` message line — never color-only; keep Drupal's inline error text.

### 5. Buttons

```css
.c9-btn {
  display: inline-block;
  padding: var(--space-3) var(--space-6);
  border-radius: var(--radius-sm);
  font-size: var(--text-base);
  font-weight: var(--weight-medium);
  border: 2px solid transparent;
  cursor: pointer;
  text-decoration: none;
  min-height: 44px; /* touch target */
}
.c9-btn--primary {
  background: var(--color-primary);
  color: var(--color-text-inverse); /* 11.3:1 */
}
.c9-btn--primary:hover { background: var(--color-primary-hover); }
.c9-btn--secondary {
  background: var(--color-bg);
  color: var(--color-primary);
  border-color: var(--color-primary);
}
.c9-btn--secondary:hover { background: var(--color-surface-brand); }
.c9-btn:focus-visible { outline: none; box-shadow: var(--focus-ring); }
```

No gold-background buttons unless the label uses `--color-text` (dark on gold, 11.1:1). Never white-on-gold.

### 6. Header / nav

Use Olivero's header region and menu system as-is (it already handles the mobile hamburger, sticky behavior, and keyboard nav). Sub-theme changes are cosmetic only:

- Header/site-branding band: `background: var(--color-primary)`, site name in `--color-text-inverse`.
- Gold identity stripe: `border-bottom: 4px solid var(--color-accent)` on the header.
- Menu links on blue: white text; hover/active state gets a `3px` gold underline (`text-decoration-color: var(--color-accent); text-underline-offset: 6px`) — not gold text.
- Focus states: let Olivero's ring stand, or apply `--focus-ring` if it clashes with the blue band.
- Mobile: no custom work — Olivero's collapse behavior handles 375px.

## Build order (~2h)

1. `tokens.css` + map Olivero primary variables (15 min)
2. Page shell + header recolor (15 min)
3. Stat card (20 min)
4. Table + badges (40 min — the real component)
5. Callout, forms, buttons (30 min)

Skipped: dark mode (Olivero has none; campus sites don't ship it), print styles (add when someone asks to print the transparency table), and a separate utilities file (YAGNI at this size).
