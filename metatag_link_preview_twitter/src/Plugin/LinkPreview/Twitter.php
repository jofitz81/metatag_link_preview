<?php

declare(strict_types=1);

namespace Drupal\metatag_link_preview_twitter\Plugin\LinkPreview;

use Drupal\Component\Utility\Html;
use Drupal\metatag_link_preview\Annotation\LinkPreview;
use Drupal\metatag_link_preview\LinkPreviewPluginBase;

/**
 * Plugin implementation of the link_preview.
 *
 * @LinkPreview(
 *   id = "twitter",
 *   label = @Translation("X"),
 *   description = @Translation("An indication of how the page will appear when shared to X (formerly Twitter).")
 * )
 */
class Twitter extends LinkPreviewPluginBase {

  /**
   * @inheritDoc
   */
  public function card(array $meta_tags): array {
    return [
      '#theme' => 'twitter_card',
      '#title' => $meta_tags['twitter_cards_title'] ?? $meta_tags['og_title'] ?? $meta_tags['title'] ?? '',
      '#image' => $meta_tags['twitter_cards_image'] ?? $meta_tags['og_image'] ?? $meta_tags['image'] ?? '',
      '#attached' => ['library' => ['metatag_link_preview_twitter/metatag_link_preview_twitter']],
    ];
  }

}
