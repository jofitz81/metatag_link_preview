<?php

declare(strict_types=1);

namespace Drupal\metatag_link_preview_open_graph\Plugin\LinkPreview;

use Drupal\Component\Utility\Html;
use Drupal\metatag_link_preview\Annotation\LinkPreview;
use Drupal\metatag_link_preview\LinkPreviewPluginBase;

/**
 * Plugin implementation of the link_preview.
 *
 * @LinkPreview(
 *   id = "facebook",
 *   label = @Translation("Facebook"),
 *   description = @Translation("An indication of how the page will appear when shared to Facebook.")
 * )
 */
class Facebook extends LinkPreviewPluginBase {

  /**
   * @inheritDoc
   */
  public function card(array $meta_tags): array {
    return [
      '#theme' => 'facebook_card',
      '#title' => $meta_tags['title'],
      '#description' => strip_tags(Html::decodeEntities($meta_tags['description'])),
      '#link' => $meta_tags['canonical_url'],
      '#image' => $meta_tags['og_image'],
      '#attached' => ['library' => ['metatag_link_preview_open_graph/metatag_link_preview_open_graph']],
    ];
  }

}
