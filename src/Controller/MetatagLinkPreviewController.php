<?php

declare(strict_types=1);

namespace Drupal\metatag_link_preview\Controller;

use Drupal\Core\Controller\ControllerBase;

class MetatagLinkPreviewController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function __invoke(): array {
    $preview_cards = [];

    $build['content'] = [
      '#theme' => 'preview_container',
      '#preview_cards' => $preview_cards,
    ];

    return $build;
  }

}
