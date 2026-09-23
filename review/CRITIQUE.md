# CRITIQUE — C9 Funding Guide plan (adversarial review, 2026-09-23)
Ranked by expected damage (severity x likelihood).

## 1. Publishing an official-branded Senate site the board never approved
**Sev: High | Likelihood: High** — the plan literally schedules board presentation AFTER public launch (Phase 7, last bullet).
Mechanism: a GitHub Pages URL with full C9 branding, "official College Nine Senate funding site" framing, policy text, meeting times/room numbers, and dollar figures (sample or not) is indistinguishable from an official publication. A screenshot circulates without the disclaimer banner; an advisor or SOAR/SGA staff asks "who authorized this?" You are one officer, not the Senate. "Sample figures pending board approval" reads to a casual viewer as *real figures pending approval* — worse than no numbers. This can burn the VPF role AND the application (Admissions hires students who respect publication chains of authority — that's the actual job).
**Cheapest mitigation:** de-officialize the demo. Title it "College Nine Funding Site — student project / proposal, not an official Senate publication" in the header, use clearly fake org names ("Sample Org A"), and get one-line written OK from the Senate chair or advisor before the URL goes anywhere. Costs 20 minutes and one Slack message.

## 2. The portfolio story is miscalibrated for the audience
**Sev: High | Likelihood: Medium-High**
Mechanism: hiring manager is a non-technical Communications Manager filling an $18.50/hr photo-cropping/page-editing role that says "willingness to learn Drupal." A DDEV + Composer + Tome + custom Twig + static-export pipeline with a narrated multi-phase video signals (a) overqualified/flight risk, (b) "will over-engineer my simple requests," or (c) — worse in 2026 — "an AI built this." The `/narrate` agent-produced video especially risks reading as not-your-work. The job's real evaluation is: can you crop a photo, edit a page, follow instructions, write clean copy.
**Cheapest mitigation:** keep the build, change the pitch. Two-line message: "I maintain my college Senate's funding pages — here's the live site and a 90-second video of me updating a page and prepping a photo." Bury the infrastructure. Cut the video to ≤2 min, screen-recorded with your own voice, showing the *admin* doing boring CMS tasks. Do not mention agents, pipelines, or static export.

## 3. Google Site ownership / two-official-sites problem
**Sev: Medium | Likelihood: High**
Mechanism: the existing Google Site is owned by someone (past officer, advisor, SUA). Launching a replacement without contacting them creates a duplicate source of truth for funding policy — students find conflicting deadlines/limits, and the owner (possibly staff) is publicly bypassed. Plan never mentions contacting them.
**Mitigation:** one email today identifying the owner and stating intent. Also answers risk #1 partially.

## 4. Phase 0 rabbit hole eats the schedule
**Sev: Medium | Likelihood: Medium-High**
Mechanism: first-time Docker/OrbStack + DDEV + Composer memory limits + Apple Silicon quirks routinely cost 3-5h, not 1.5h. The plan's fallback ("read quick-start docs") is not a fallback — it's the same path. 10-15h total against a hard Oct 5 deadline with classes starting: one bad evening kills Phases 5-7, which are the phases the hiring manager actually sees.
**Mitigation:** hard fallback that changes tools, not docs: if Drupal isn't installed by hour 3, ship the same content as a hand-written HTML/CSS static site on Pages (directly demonstrates the listed HTML/CSS requirement!) and say "currently learning Drupal via DDEV" — which is literally what the posting asks for. Willingness-to-learn > half-broken expertise.

## 5. Tome + Webform static export gotchas
**Sev: Low-Medium | Likelihood: Medium**
Mechanism: plan hand-waves "known pattern." Tome also chokes on: aggregated Views with exposed filters, file-upload fields, path aliases, and CSS/JS aggregation settings; the Views SUM/grouping in Phase 2 is exactly the kind of dynamic page that exports subtly wrong (stale totals are fine; broken pager/filters look sloppy).
**Mitigation:** run `tome:static` at end of Phase 2, not Phase 6 — find export breakage while there's time to simplify the View.

## 6. Interview-story backfires
**Sev: Medium | Likelihood: Medium**
- "I built this for my Senate role" invites "did the Senate adopt it?" — answer today is no. Get at least a "presented, well-received" before Oct 5 or frame as "proposal I'm bringing to the board Oct X" (honest, shows initiative).
- Policy page as writing sample: those policies aren't yours to publish; auto-denial rules from internal email templates going public may itself annoy the board.
- If asked to reproduce anything live (crop a photo, edit a page) you must be able to do it cold, without agents. Practice the Photopea flow twice unassisted.

## Cheaper alternative (the 20% version)
Static HTML/CSS site with sample transparency table + Google Form embed + 3 well-cropped photos + honest cover note ships in ~5h, hits every listed job requirement (HTML/CSS, photos, willingness to learn Drupal), and carries zero political risk. The Drupal build's marginal value is (a) real VPF tooling and (b) the Drupal talking point — real, but it must not crowd out risks 1-3.

## Steelman
Building in the employer's actual CMS, driven by a real mandate with a real stakeholder, is a genuinely strong differentiator most applicants won't have — and the transparency dashboard is real VPF work either way. The plan's phase ordering, timeboxes, and out-of-scope list are unusually disciplined.

## Verdict
**Proceed-with-changes.** The build survives; the *publication and pitch* don't.
Before writing any code:
1. Get chair/advisor one-line OK + identify Google Site owner (risk 1, 3).
2. Rewrite demo header/framing as proposal-not-official; fake org names (risk 1).
3. Pre-commit the hour-3 HTML fallback and the humble 2-min pitch (risks 2, 4).
Early-warning signs: Phase 0 past 3h; anyone outside eboard shares the URL; video draft exceeds 2.5 min or mentions tooling.
