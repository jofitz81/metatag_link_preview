<?php

declare(strict_types=1);

namespace Drupal\metatag_link_preview\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Utility\Token;
use Drupal\metatag\MetatagManagerInterface;
use Drupal\metatag_link_preview\LinkPreviewPluginManager;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class MetatagLinkPreviewBaseController extends ControllerBase {

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
  protected function build(ContentEntityInterface $entity): array {
    $preview_cards = [];

    $meta_tags = $this->_getMetaTags($entity);

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

  protected function _getMetaTags(ContentEntityInterface $entity): array|bool {
    if (!$this->_hasMetaTagField($entity)) {
      return FALSE;
    }

    $meta_tags = $this->metatagManager->tagsFromEntityWithDefaults($entity);
    return $this->_replaceTokens($meta_tags, $entity);
  }

  protected function _hasMetaTagField(ContentEntityInterface $entity): bool {
    foreach ($entity->getFieldDefinitions() as $field_definition) {
      if ($field_definition->getType() === 'metatag') {
        return TRUE;
      }
    }
    return FALSE;
  }

  protected function _replaceTokens(array $meta_tags, ContentEntityInterface $entity): array {
    $data = [$entity->getEntityTypeId() => $entity];
    foreach ($meta_tags as &$meta_tag) {
      $meta_tag = $this->tokenService->replace($meta_tag, $data);
    }
    return $meta_tags;
  }

}
