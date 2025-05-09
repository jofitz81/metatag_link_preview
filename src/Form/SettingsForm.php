<?php

declare(strict_types=1);

namespace Drupal\metatag_link_preview\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\metatag_link_preview\LinkPreviewPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Metatag Link Preview settings for this site.
 */
final class SettingsForm extends ConfigFormBase {

  protected $linkPreviewPlugins;

  /**
   * @inheritDoc
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    TypedConfigManagerInterface $typedConfigManager,
    private readonly LinkPreviewPluginManager $linkPreviewManager,
  ) {
    parent::__construct($config_factory, $typedConfigManager);
  }

  /**
   * @inheritDoc
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('config.typed'),
      $container->get('plugin.manager.link_preview'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'metatag_link_preview_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['metatag_link_preview.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['enabled_previews'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Enabled link previews'),
      '#description' => $this->t('Enable/disable link previews to be displayed on entities.'),
    ];
    foreach ($this->getLinkPreviewPlugins() as $plugin_id => $definition) {
      $setting = $this->config('metatag_link_preview.settings')->get("enabled_previews.$plugin_id");
      $disabled = $setting === 0;
      $form['enabled_previews'][$plugin_id] = [
        '#type' => 'checkbox',
        '#title' => $definition['label'],
        '#default_value' => !$disabled,
      ];
    }
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    foreach ($this->getLinkPreviewPlugins() as $plugin_id => $definition) {
      $this->config('metatag_link_preview.settings')
        ->set("enabled_previews.$plugin_id", $form_state->getValue($plugin_id))
        ->save();
    }
    parent::submitForm($form, $form_state);
  }

  protected function getLinkPreviewPlugins() {
    if (!isset($this->linkPreviewPlugins)) {
      $this->linkPreviewPlugins = $this->linkPreviewManager->getDefinitions();
    }
    return $this->linkPreviewPlugins;
  }

}
