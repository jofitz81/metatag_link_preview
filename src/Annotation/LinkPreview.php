<?php

declare(strict_types=1);

namespace Drupal\metatag_link_preview\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines link_preview annotation object.
 *
 * @Annotation
 */
final class LinkPreview extends Plugin {

  /**
   * The plugin ID.
   */
  public readonly string $id;

  /**
   * The human-readable name of the plugin.
   *
   * @ingroup plugin_translatable
   */
  public readonly string $title;

  /**
   * The description of the plugin.
   *
   * @ingroup plugin_translatable
   */
  public readonly string $description;

}
