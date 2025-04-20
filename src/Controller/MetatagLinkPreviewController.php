<?php

declare(strict_types=1);

namespace Drupal\metatag_link_preview\Controller;

use Drupal\Component\Utility\Html;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Utility\Token;
use Drupal\metatag\MetatagManagerInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MetatagLinkPreviewController extends ControllerBase {

  public function __construct(
    private readonly MetatagManagerInterface $metatagManager,
    private readonly Token $tokenService,
  ) {}

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('metatag.manager'),
      $container->get('token'),
    );
  }

  /**
   * Builds the response.
   */
  public function __invoke(NodeInterface $node): array {
    $preview_cards = [];

    $meta_tags = $this->_getMetaTags($node);
    $preview_cards[] = [
      '#theme' => 'search_engine_card',
      '#title' => $meta_tags['title'],
      '#link' => $meta_tags['canonical_url'],
      '#description' => strip_tags(Html::decodeEntities($meta_tags['description'])),
    ];

    $build['content'] = [
      '#theme' => 'preview_container',
      '#preview_cards' => $preview_cards,
    ];

    $build['#attached']['library'][] = 'metatag_link_preview/metatag_link_preview';

    return $build;
  }

  protected function _getMetaTags(NodeInterface $node): array|bool {
    if (!$this->_hasMetaTagField($node)) {
      return FALSE;
    }

    $meta_tags = $this->metatagManager->tagsFromEntityWithDefaults($node);
    return $this->_replaceTokens($meta_tags, $node);
  }

  protected function _hasMetaTagField(NodeInterface $node): bool {
    foreach ($node->getFieldDefinitions() as $field_definition) {
      if ($field_definition->getType() === 'metatag') {
        return TRUE;
      }
    }
    return FALSE;
  }

  protected function _replaceTokens(array $meta_tags, NodeInterface $node): array {
    foreach ($meta_tags as &$meta_tag) {
      $data = ['node' => $node];
      $meta_tag = $this->tokenService->replace($meta_tag, $data);
    }
    return $meta_tags;
  }

}
