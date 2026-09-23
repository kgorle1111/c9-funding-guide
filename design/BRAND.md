# College Nine Senate Funding Site — Brand Guide

Official student-government site (VP of Finance, Drupal 11). Displays budget and funding data.
Two poles to balance: **institutional trust** (UCSC Blue anchors everything financial) and
**student warmth** (gold accents, plain language, globe identity).

Verified 2026-09-23 against UCSC Communications & Marketing brand pages: UCSC Blue `#003c6c`
(PMS 541C, RGB 0/60/108) and UCSC Gold/Yellow `#fdc700` (PMS 116C, RGB 253/199/0) are correct.
Note: UCSC officially calls the gold "Primary Yellow."

## 1. Color palette

### Primary
| Token | Hex | Use |
|---|---|---|
| `--c9-blue` | `#003c6c` | Headers, nav, primary buttons, table headers, footer |
| `--c9-blue-dark` | `#00284a` | Hover states on blue, footer background |
| `--c9-blue-light` | `#dbe8f2` | Tinted backgrounds, table row stripes, info callouts |
| `--c9-gold` | `#fdc700` | Accents only: highlights, active-state underlines, "funded" badges |
| `--c9-gold-tint` | `#fff4cc` | Soft highlight backgrounds (e.g., deadline banners) |

### Secondary (globe theme, use sparingly)
| Token | Hex | Use |
|---|---|---|
| `--c9-teal` | `#00707d` | Links on white (distinct from headings), secondary charts |
| `--c9-green` | `#2e7d32` | Approved / positive budget deltas |
| `--c9-red` | `#b3261e` | Denied / negative deltas / validation errors |

### Neutrals
| Token | Hex | Use |
|---|---|---|
| `--c9-ink` | `#1a1a1a` | Body text |
| `--c9-gray-700` | `#4a5560` | Secondary text, captions, table metadata |
| `--c9-gray-300` | `#c9d1d9` | Borders, dividers, table rules |
| `--c9-gray-100` | `#f4f6f8` | Page/section backgrounds |
| `--c9-white` | `#ffffff` | Cards, content surfaces |

### Accessible text-on-color pairings (WCAG AA, contrast ratio shown)
| Background | Text | Ratio | Verdict |
|---|---|---|---|
| `#003c6c` | `#ffffff` | 10.4:1 | Pass (AAA) — default for blue surfaces |
| `#003c6c` | `#fdc700` | 6.3:1 | Pass — gold text/accents on blue OK |
| `#fdc700` | `#003c6c` | 6.3:1 | Pass — required text color on gold; never white-on-gold (1.6:1, fails) |
| `#ffffff` | `#00707d` | 5.6:1 | Pass — link color |
| `#ffffff` | `#4a5560` | 7.4:1 | Pass — secondary text |
| `#dbe8f2` | `#003c6c` | 8.5:1 | Pass — callout text |

Rule: gold is never a text color on white and white is never text on gold. Gold carries
attention, blue carries information.

## 2. Typography (Google Fonts)

UCSC's brand uses Roboto as its web/UI family, so we harmonize by adopting it directly,
with Roboto Condensed for display weight (echoes UCSC headline style).

| Role | Font | Weights | Notes |
|---|---|---|---|
| Display / H1–H2 | Roboto Condensed | 700 | All headings in `--c9-blue` on light surfaces |
| H3–H6, UI, body | Roboto | 400, 500, 700 | 16px base, 1.6 line-height for policy text |
| Budget figures / tables | Roboto Mono | 400, 500 | Tabular numerals; right-align currency columns |

Fallback stacks: `"Roboto Condensed", "Arial Narrow", sans-serif`; `Roboto, Helvetica, Arial,
sans-serif`; `"Roboto Mono", ui-monospace, monospace`.
Scale (rem): 2.25 / 1.75 / 1.375 / 1.125 / 1 / 0.875. Max body line length ~70ch.

## 3. Voice & tone

Voice: a knowledgeable peer, not an administrator. Clear, warm, direct. Second person ("you"),
active voice, short sentences. Every policy page answers three things fast: am I eligible,
how much, by when.

- Say "Apply by Friday, May 8 at 5 p.m." — never "applications must be submitted no later than."
- Numbers are exact: "$1,200 remaining in the events fund," not "limited funds remain."
- Name the human: "Questions? Email the VP of Finance," not "contact the appropriate officer."
- Banned: "pursuant to," "aforementioned," "utilize," "in order to," "please be advised."
- Funding decisions: state the outcome first, the reason second, the next step third.
- Tone shifts by context: neutral and precise on budget tables; warmer on how-to and welcome
  pages; never jokey around money or denials.

## 4. Logo usage

- The College Nine globe logo is the site's primary mark: header top-left, links to home.
- Give it clear space equal to the globe's radius on all sides; minimum render 32px height.
- Use it on white or `--c9-blue` backgrounds only; never recolor it, never place on gold.
- Pair with the UCSC wordmark in the footer and on official documents (funding decision
  letters, annual budget PDF) — UCSC mark first or above, per campus co-branding convention.
  Do not pair in the main nav; one mark per surface keeps hierarchy clean.
- Never stretch, add effects, or crop the globe. SVG preferred in Drupal theme assets.

## 5. Do / Don't

1. **Do** show budget balances as plain numbers with a green/red delta chip.
   **Don't** bury figures in prose ("the fund has seen considerable utilization this quarter").
2. **Do** use gold as a thin accent — active tab underline, deadline banner background
   (`#fff4cc` with `#003c6c` text). **Don't** set paragraphs or buttons in solid gold with
   white text; it fails contrast and reads as alarm.
3. **Do** write "Your request was approved for $450. Funds arrive in 5–7 days."
   **Don't** write "The Senate has determined that the aforementioned allocation request
   satisfies the requisite criteria."

Sources: [UCSC Color](https://communications.ucsc.edu/brand-overview/color/), [UCSC Brand Guide](https://communications.ucsc.edu/brand-overview/)
