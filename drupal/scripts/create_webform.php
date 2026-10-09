<?php

/**
 * Funding request webform + /request-funding page link in the main menu.
 * Run: ddev drush php:script drupal/scripts/create_webform.php  (idempotent)
 */

use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\webform\Entity\Webform;

if (Webform::load('funding_request')) {
  print "webform exists\n";
  return;
}

$elements = <<<'YAML'
intro:
  '#type': processed_text
  '#format': full_html
  '#text': |
    <div class="c9-callout"><strong>Before you start:</strong> read the
    <a href="/funding-guidelines">funding guidelines</a> — a few minutes now saves a resubmission later.</div>
    <div class="c9-callout c9-callout--warning"><strong>Request limit:</strong> organizations outside
    College Nine can request up to $200. Requesting more is allowed — a detailed written justification
    field will appear below.</div>
org_name:
  '#type': textfield
  '#title': Organization name
  '#required': true
contact_name:
  '#type': textfield
  '#title': Contact person
  '#required': true
contact_email:
  '#type': email
  '#title': UCSC email
  '#required': true
c9_affiliate:
  '#type': radios
  '#title': Is your organization based at College Nine?
  '#options':
    'Yes': 'Yes'
    'No': 'No'
  '#required': true
event_name:
  '#type': textfield
  '#title': Event or project name
  '#required': true
event_date:
  '#type': date
  '#title': Event date
  '#required': true
amount:
  '#type': number
  '#title': Amount requested (USD)
  '#min': 1
  '#step': 0.01
  '#required': true
justification:
  '#type': textarea
  '#title': Written justification for exceeding the $200 limit
  '#description': 'Required for outside organizations requesting more than $200. Explain where each dollar above the limit goes.'
  '#states':
    visible:
      ':input[name="c9_affiliate"]':
        value: 'No'
      ':input[name="amount"]':
        value:
          greater: '200'
    required:
      ':input[name="c9_affiliate"]':
        value: 'No'
      ':input[name="amount"]':
        value:
          greater: '200'
budget_breakdown:
  '#type': textarea
  '#title': Itemized budget
  '#description': 'Where each dollar goes. Reminder: food costs cannot be funded.'
  '#required': true
guidelines_read:
  '#type': checkbox
  '#title': I have read the funding guidelines
  '#required': true
outro:
  '#type': processed_text
  '#format': full_html
  '#text': |
    <p><strong>What happens next:</strong> we email you within 1–2 days to schedule your
    3–5 minute presentation (Thursdays 6:00–7:30pm, Social Sciences 1, Room 261).
    Decisions arrive by email, typically within 2–3 weeks. Questions?
    <a href="mailto:c9senate@ucsc.edu">c9senate@ucsc.edu</a></p>
YAML;

$webform = Webform::create([
  'id' => 'funding_request',
  'title' => 'Funding Request',
  'elements' => $elements,
  'settings' => [
    'confirmation_type' => 'page',
    'confirmation_title' => 'Your request is in',
    'confirmation_message' => 'Thank you! Keep an eye on your UCSC email — we will reach out within 1–2 days to schedule your presentation at a Thursday Senate meeting (6:00–7:30pm, Social Sciences 1, Room 261).',
  ],
]);
$webform->save();

// Email handler -> Senate inbox (Mailpit catches it in DDEV; no real mail sent
// locally). Set as raw config: Webform has no setHandlers() method.
$webform->set('handlers', [
  'email_senate' => [
    'id' => 'email',
    'handler_id' => 'email_senate',
    'label' => 'Email Senate',
    'status' => TRUE,
    'weight' => 0,
    'conditions' => [],
    'settings' => [
      'to_mail' => 'c9senate@ucsc.edu',
      'subject' => '[webform_submission:values:org_name:raw] - new funding request',
    ],
  ],
]);
$webform->save();

MenuLinkContent::create([
  'title' => 'Request Funding',
  'link' => ['uri' => 'internal:/form/funding-request'],
  'menu_name' => 'main',
  'weight' => 5,
])->save();

print "webform + menu link created\n";
