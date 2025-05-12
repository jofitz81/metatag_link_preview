<?php

declare(strict_types=1);

namespace Drupal\metatag_link_preview_open_graph\Plugin\LinkPreview;

use Drupal\metatag_link_preview\Annotation\LinkPreview;

/**
 * Plugin implementation of the link_preview.
 *
 * @LinkPreview(
 *   id = "facebook",
 *   label = @Translation("Facebook"),
 *   description = @Translation("An indication of how the page will appear when shared to Facebook.")
 * )
 */
class Facebook extends LinkPreviewOpenGraphPluginBase {

  /**
   * @inheritDoc
   */
  public function card(array $meta_tags): array {
    $render_array = $this->processMetaTags($meta_tags);
    $render_array['#theme'] = 'facebook_card';
    return $render_array;
  }

}
