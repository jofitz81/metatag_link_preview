<?php

declare(strict_types=1);

namespace Drupal\metatag_link_preview;

/**
 * Interface for link_preview plugins.
 */
interface LinkPreviewInterface {

  /**
   * Returns the Link Preview card.
   */
  public function card(array $meta_tags): array;

  /**
   * Processes meta tags to be displayed on the Link Preview card.
   */
  public function processMetaTags(array $meta_tags): array;

}
