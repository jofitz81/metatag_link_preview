<?php

declare(strict_types=1);

namespace Drupal\metatag_link_preview;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\metatag_link_preview\Annotation\LinkPreview;

/**
 * LinkPreview plugin manager.
 */
final class LinkPreviewPluginManager extends DefaultPluginManager {

  /**
   * Constructs the object.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/LinkPreview', $namespaces, $module_handler, LinkPreviewInterface::class, LinkPreview::class);
    $this->alterInfo('link_preview_info');
    $this->setCacheBackend($cache_backend, 'link_preview_plugins');
  }

}
