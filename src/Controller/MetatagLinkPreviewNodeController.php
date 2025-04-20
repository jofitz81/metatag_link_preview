<?php

declare(strict_types=1);

namespace Drupal\metatag_link_preview\Controller;

use Drupal\Core\Entity\ContentEntityInterface;

class MetatagLinkPreviewNodeController extends MetatagLinkPreviewBaseController {

  /**
   * Builds the response.
   */
  public function __invoke(ContentEntityInterface $node): array {
    return parent::build($node);
  }

}
