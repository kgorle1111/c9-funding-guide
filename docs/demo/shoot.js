// Scene screenshots for the demo video (12 scenes).
// Run: NODE_PATH=<playwright dir> node docs/demo/shoot.js '<one-time-login-url>'
const { chromium } = require('playwright');

const BASE = 'http://c9.localhost';
const OUT = __dirname + '/scenes/';

(async () => {
  const uli = process.argv[2];
  const browser = await chromium.launch({ channel: 'chrome' });

  // ---- public scenes (logged out) ----
  const pub = await browser.newPage({ viewport: { width: 1440, height: 900 } });
  const shot = async (p, name) => { await p.waitForTimeout(650); await p.screenshot({ path: OUT + name + '.png' }); console.log('shot', name); };

  await pub.goto(BASE + '/');
  await shot(pub, '01-home');

  await pub.goto(BASE + '/how-to-request');
  await shot(pub, '03-how-to');

  await pub.goto(BASE + '/funding-guidelines');
  await pub.evaluate(() => window.scrollBy(0, 500));
  await shot(pub, '04-guidelines');

  await pub.goto(BASE + '/faq');
  await pub.evaluate(() => window.scrollBy(0, 600));
  await shot(pub, '05-faq');

  await pub.goto(BASE + '/transparency');
  await pub.locator('.c9-stat-row').scrollIntoViewIfNeeded();
  await shot(pub, '06-transparency-stats');
  await pub.evaluate(() => window.scrollBy(0, 700));
  await shot(pub, '07-transparency-table');

  await pub.goto(BASE + '/form/funding-request');
  await shot(pub, '08-form');

  await pub.fill('input[name=org_name]', 'Coastal Cultures Collective');
  await pub.fill('input[name=contact_name]', 'Priya Sharma');
  await pub.fill('input[name=contact_email]', 'psharma@ucsc.edu');
  await pub.check('input[name=c9_affiliate][value=No]');
  await pub.fill('input[name=event_name]', 'Spring Showcase');
  await pub.fill('input[name=event_date]', '2026-11-20');
  await pub.fill('input[name=amount]', '350');
  await pub.dispatchEvent('input[name=amount]', 'change');
  await pub.waitForTimeout(500);
  await pub.fill('textarea[name=justification]', 'Venue rental is $220 and performer stipends are $130. We expect 60+ attendees, at least 20 from College Nine.');
  await pub.fill('textarea[name=budget_breakdown]', 'Venue $220, stipends $130. No food costs requested.');
  await pub.check('input[name=guidelines_read]');
  await pub.locator('textarea[name=justification]').scrollIntoViewIfNeeded();
  await shot(pub, '09-form-filled');

  await pub.click('input[type=submit],button[type=submit]');
  await pub.waitForTimeout(1400);
  await shot(pub, '10-confirmation');

  // mobile
  const mob = await browser.newPage({ viewport: { width: 390, height: 844 } });
  await mob.goto(BASE + '/transparency');
  await mob.locator('.c9-stat-card--hero').scrollIntoViewIfNeeded();
  await shot(mob, '02-mobile');

  // ---- admin scenes (logged in) ----
  if (uli) {
    const adm = await browser.newPage({ viewport: { width: 1440, height: 900 } });
    await adm.goto(BASE + new URL(uli).pathname);
    await adm.waitForTimeout(900);
    await adm.goto(BASE + '/node/16/edit');
    await shot(adm, '11-admin-edit');
    await adm.goto(BASE + '/admin/content');
    await shot(adm, '12-admin-content');
  }

  await browser.close();
})();
