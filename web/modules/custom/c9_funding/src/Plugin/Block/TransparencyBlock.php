<?php

namespace Drupal\c9_funding\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Funding transparency dashboard: budget stats + per-quarter awards table.
 *
 * @Block(
 *   id = "c9_transparency",
 *   admin_label = @Translation("C9 Funding Transparency"),
 * )
 */
class TransparencyBlock extends BlockBase {

  // kn: sample annual budget for the public demo; becomes a block setting
  // wired to the real Senate budget once the board approves real figures.
  const ANNUAL_BUDGET = 5000.00;

  public function build(): array {
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $nids = $storage->getQuery()
      ->condition('type', 'funding_award')
      ->condition('status', 1)
      ->sort('field_quarter')
      ->accessCheck(TRUE)
      ->execute();
    $awards = $storage->loadMultiple($nids);

    $quarters = ['fall' => 'Fall', 'winter' => 'Winter', 'spring' => 'Spring'];
    $by_quarter = array_fill_keys(array_keys($quarters), []);
    $spent = 0.0;
    foreach ($awards as $award) {
      $q = $award->get('field_quarter')->value;
      $amount = (float) $award->get('field_amount')->value;
      $by_quarter[$q][] = [
        'org' => $award->get('field_organization')->value,
        'purpose' => $award->get('field_purpose')->value,
        'amount' => $amount,
        'status' => $award->get('field_status')->value,
      ];
      if ($award->get('field_status')->value === 'awarded') {
        $spent += $amount;
      }
    }
    $remaining = self::ANNUAL_BUDGET - $spent;

    $fmt = fn(float $n): string => '$' . number_format($n, 2);

    $stats = [
      '#type' => 'inline_template',
      '#template' => '
        <div class="c9-stat-row">
          <div class="c9-stat-card c9-stat-card--hero">
            <span class="c9-stat-card__label">{{ "Remaining this year"|t }}</span>
            <span class="c9-stat-card__value">{{ remaining }}</span>
          </div>
          <div class="c9-stat-card">
            <span class="c9-stat-card__label">{{ "Annual budget"|t }}</span>
            <span class="c9-stat-card__value">{{ budget }}</span>
          </div>
          <div class="c9-stat-card">
            <span class="c9-stat-card__label">{{ "Awarded so far"|t }}</span>
            <span class="c9-stat-card__value">{{ spent }}</span>
          </div>
        </div>',
      '#context' => [
        'remaining' => $fmt($remaining),
        'budget' => $fmt(self::ANNUAL_BUDGET),
        'spent' => $fmt($spent),
      ],
    ];

    $rows = [];
    foreach ($quarters as $key => $label) {
      if (!$by_quarter[$key]) {
        continue;
      }
      $q_total = 0.0;
      foreach ($by_quarter[$key] as $r) {
        $badge = $r['status'] === 'awarded' ? 'awarded' : 'denied';
        $rows[] = [
          'data' => [
            $label,
            $r['org'],
            $r['purpose'],
            ['data' => ['#markup' => '<span class="c9-badge c9-badge--' . $badge . '">' . ucfirst($badge) . '</span>']],
            ['data' => $fmt($r['amount']), 'class' => ['c9-amount']],
          ],
        ];
        if ($r['status'] === 'awarded') {
          $q_total += $r['amount'];
        }
      }
      $rows[] = [
        'data' => [
          ['data' => $label . ' total', 'colspan' => 4, 'class' => ['c9-sum-label']],
          ['data' => $fmt($q_total), 'class' => ['c9-amount', 'c9-sum']],
        ],
        'class' => ['c9-sum-row'],
      ];
    }

    $table = [
      '#type' => 'table',
      '#header' => [$this->t('Quarter'), $this->t('Organization'), $this->t('Purpose'), $this->t('Status'), $this->t('Amount')],
      '#rows' => $rows,
      '#attributes' => ['class' => ['c9-awards-table']],
      '#prefix' => '<div class="c9-table-scroll">',
      '#suffix' => '</div>',
    ];

    return [
      'stats' => $stats,
      'table' => $table,
      // Number must refresh whenever any award is created/edited/deleted.
      '#cache' => ['tags' => ['node_list:funding_award']],
    ];
  }

}
