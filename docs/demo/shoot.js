// Scene screenshots for the demo video. Run:
//   npx --yes playwright screenshot --help  (sanity)
//   node docs/demo/shoot.js '<one-time-login-url>'
// Uses the installed Chrome (channel) — no browser download.
const { chromium } = require('playwright');

const BASE = 'http://c9.localhost';
const OUT = __dirname + '/scenes/';

(async () => {
  const uli = process.argv[2];
  const browser = await chromium.launch({ channel: 'chrome' });
  const page = await browser.newPage({ viewport: { width: 1440, height: 900 } });
  const shot = async (name) => { await page.waitForTimeout(600); await page.screenshot({ path: OUT + name + '.png' }); console.log('shot', name); };

  // login (uli is on the ddev.site host; session cookie is per-host, so log in on c9.localhost via the same path)
  if (uli) {
    const path = new URL(uli).pathname;
    await page.goto(BASE + path);
    await page.waitForTimeout(800);
  }

  await page.goto(BASE + '/');
  await shot('01-home');

  await page.goto(BASE + '/node/16/edit');
  await shot('02-admin-edit');

  await page.goto(BASE + '/transparency');
  await page.locator('.c9-stat-row').scrollIntoViewIfNeeded();
  await shot('03-transparency');

  await page.goto(BASE + '/form/funding-request');
  await shot('04-form');

  await page.fill('input[name="org_name"]', 'Coastal Cultures Collective');
  await page.fill('input[name="contact_name"]', 'Priya Sharma');
  await page.fill('input[name="contact_email"]', 'psharma@ucsc.edu');
  await page.check('input[name="c9_affiliate"][value="No"]');
  await page.fill('input[name="event_name"]', 'Spring Showcase');
  await page.fill('input[name="event_date"]', '2026-11-20');
  await page.fill('input[name="amount"]', '350');
  await page.waitForTimeout(400); // conditional justification appears
  await page.fill('textarea[name="justification"]', 'Venue rental is $220 and performer stipends are $130. We expect 60+ attendees, at least 20 from College Nine.');
  await page.fill('textarea[name="budget_breakdown"]', 'Venue $220, stipends $130. No food costs requested.');
  await page.check('input[name="guidelines_read"]');
  await page.locator('textarea[name="justification"]').scrollIntoViewIfNeeded();
  await shot('05-form-filled');

  await page.click('input[type="submit"], button[type="submit"]');
  await page.waitForTimeout(1200);
  await shot('06-confirmation');

  await page.goto(BASE + '/funding-guidelines');
  await shot('07-guidelines');

  await browser.close();
})();
