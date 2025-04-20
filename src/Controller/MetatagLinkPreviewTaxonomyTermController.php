<?php

declare(strict_types=1);

namespace Drupal\metatag_link_preview\Controller;

use Drupal\Core\Entity\ContentEntityInterface;

class MetatagLinkPreviewTaxonomyTermController extends MetatagLinkPreviewBaseController {

  /**
   * Builds the response.
   */
  public function __invoke(ContentEntityInterface $taxonomy_term): array {
    return parent::build($taxonomy_term);
  }

}
