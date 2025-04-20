<?php

declare(strict_types=1);

namespace Drupal\metatag_link_preview\Plugin\LinkPreview;

use Drupal\Component\Utility\Html;
use Drupal\metatag_link_preview\Annotation\LinkPreview;
use Drupal\metatag_link_preview\LinkPreviewPluginBase;

/**
 * Plugin implementation of the link_preview.
 *
 * @LinkPreview(
 *   id = "search_engine",
 *   label = @Translation("Search engine"),
 *   description = @Translation("An indication of how the page will appear in a search engine.")
 * )
 */
class SearchEngine extends LinkPreviewPluginBase {

  /**
   * @inheritDoc
   */
  public function card(array $meta_tags): array {
    return [
      '#theme' => 'search_engine_card',
      '#title' => $meta_tags['title'],
      '#link' => $meta_tags['canonical_url'],
      '#description' => strip_tags(Html::decodeEntities($meta_tags['description'])),
      '#attached' => ['library' => ['metatag_link_preview/metatag_link_preview']],
    ];
  }

}
