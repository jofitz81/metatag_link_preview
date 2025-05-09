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
      '#meta_tag_values' => $this->_getMetaTagValues($meta_tags),
      '#preview_cards' => $preview_cards,
    ];

    return $build;
  }

  protected function _getMetaTags(ContentEntityInterface $entity): array|bool {
    if (!$this->_hasMetaTagField($entity)) {
      return FALSE;
    }

    $meta_tags = $this->metatagManager->tagsFromEntityWithDefaults($entity);

    if ($this->_entityIsFrontPage($entity)) {
      $front_meta_tags = $this->entityTypeManager()
        ->getStorage('metatag_defaults')
        ->load('front')
        ->get('tags');
      $meta_tags = array_merge($meta_tags, $front_meta_tags);
    }

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

  public function _entityIsFrontPage(ContentEntityInterface $entity) {
    $is_front_page = FALSE;
    $url = $entity->toUrl();
    if ($url->getRouteName()) {
      $entity_path = '/' . $url->getInternalPath();
      $front_page_path = $this->config('system.site')->get('page.front');
      $is_front_page = $entity_path === $front_page_path;
    }
    return $is_front_page;
  }

  protected function _getMetaTagValues(bool|array $meta_tags): array {
    $grouped_tags = [];
    $basic_tags = ['title', 'description', 'canonical'];
    foreach ($meta_tags as $key => $value) {
      [$tag_type] = explode('_', $key);
      $text = "<p><strong>$key</strong>: $value</p>";
      switch ($tag_type) {
        case 'og':
        case 'schema':
        case 'twitter':
          $grouped_tags[$tag_type][] = $text;
          break;

        default:
          if (in_array($tag_type, $basic_tags)) {
            $grouped_tags['basic'][] = $text;
          }
          else {
            $grouped_tags['other'][] = $text;
          }
      }
    }
    $tag_type_order = [
      'basic',
      'og',
      'twitter',
      'other',
      'schema',
    ];
    $grouped_tags = array_replace(array_flip($tag_type_order), $grouped_tags);
    $meta_tag_values = [];
    foreach ($grouped_tags as $tag_type => $tags) {
      $meta_tag_group = [
        '#type' => 'details',
        '#title' => $tag_type,
        '#open' => FALSE,
        'grouped_tags' => ['#markup' => ''],
      ];
      foreach ($tags as $tag_name => $tag_values) {
        $meta_tag_group['grouped_tags']['#markup'] .= $tag_values;
      }
      $meta_tag_values[] = $meta_tag_group;
    }
    return $meta_tag_values;
  }

}
