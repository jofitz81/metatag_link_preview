<?php

declare(strict_types=1);

namespace Drupal\metatag_link_preview\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Utility\Token;
use Drupal\metatag\MetatagManagerInterface;
use Drupal\metatag_link_preview\LinkPreviewPluginManager;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MetatagLinkPreviewController extends ControllerBase {

  public function __construct(
    private readonly MetatagManagerInterface $metatagManager,
    private readonly Token $tokenService,
    private readonly LinkPreviewPluginManager $linkPreviewManager,
  ) {}

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('metatag.manager'),
      $container->get('token'),
      $container->get('plugin.manager.link_preview'),
    );
  }

  /**
   * Builds the response.
   */
  public function __invoke(NodeInterface $node): array {
    $preview_cards = [];

    $meta_tags = $this->_getMetaTags($node);

    foreach ($this->linkPreviewManager->getDefinitions() as $plugin_id => $definition) {
      $link_preview = $this->linkPreviewManager->createInstance($plugin_id);
      $preview_cards[] = $link_preview->card($meta_tags);
    }

    $build['content'] = [
      '#theme' => 'preview_container',
      '#preview_cards' => $preview_cards,
    ];

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
