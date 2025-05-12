<?php

declare(strict_types=1);

namespace Drupal\metatag_link_preview_open_graph\Plugin\LinkPreview;

use Drupal\metatag_link_preview\Annotation\LinkPreview;
use Drupal\metatag_link_preview\LinkPreviewPluginBase;

/**
 * Plugin implementation of the link_preview.
 *
 * @LinkPreview(
 *   id = "linkedin",
 *   label = @Translation("LinkedIn"),
 *   description = @Translation("An indication of how the page will appear when shared to LinkedIn.")
 * )
 */
class Linkedin extends LinkPreviewPluginBase {

  /**
   * @inheritDoc
   */
  public function card(array $meta_tags): array {
    return [
      '#theme' => 'linkedin_card',
      '#title' => $meta_tags['og_title'],
      '#link' => $meta_tags['og_url'],
      '#image' => $meta_tags['og_image'],
      '#attached' => ['library' => ['metatag_link_preview_open_graph/metatag_link_preview_open_graph']],
    ];
  }

}
