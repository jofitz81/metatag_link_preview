<?php

declare(strict_types=1);

namespace Drupal\metatag_link_preview_open_graph\Plugin\LinkPreview;

use Drupal\Component\Utility\Html;
use Drupal\metatag_link_preview\Annotation\LinkPreview;
use Drupal\metatag_link_preview\LinkPreviewPluginBase;

/**
 * Base class for link_preview plugins.
 */
abstract class LinkPreviewOpenGraphPluginBase extends LinkPreviewPluginBase {

  public function processMetaTags(array $meta_tags): array {
    $description = $meta_tags['og_description'] ?? $meta_tags['description'] ?? '';
    return [
      '#title' => $meta_tags['og_title'] ?? $meta_tags['title'] ?? '',
      '#description' => strip_tags(Html::decodeEntities($description)),
      '#link' => $meta_tags['og_url'] ?? $meta_tags['canonical_url'] ?? '',
      '#image' => $meta_tags['og_image'] ?? $meta_tags['og_image_url'] ?? $meta_tags['image'] ?? '',
      '#attached' => ['library' => ['metatag_link_preview_open_graph/metatag_link_preview_open_graph']],
    ];
  }

}
