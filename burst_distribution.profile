<?php

use Drupal\node\NodeInterface;

function burst_distribution_install_tasks() {
  return [
    'burst_distribution_install_burst_theme' => []
  ];
}

function burst_distribution_install_burst_theme() {
  \Drupal::configFactory()
  ->getEditable('system.theme')
  ->set('default', 'claro')
  ->save();

  /** @var \Drupal\Core\Extension\ThemeInstaller $theme_installer */
  $theme_installer = \Drupal::service('theme_installer');
  $theme_installer->install(['burst_theme']);

  \Drupal::configFactory()
  ->getEditable('system.theme')
  ->set('default', 'burst_theme')
  ->save();
}

/**
 * Implements hook_ENTITY_TYPE_presave()
 *
 * @param \Drupal\node\NodeInterface $node
 */
function burst_distribution_node_update(NodeInterface $node) {
  // Clear the cache if the publication status has changed.
  if ($node->original->isPublished() !== $node->isPublished()) {
    drupal_flush_all_caches();
    \Drupal::messenger()->addStatus(t('The cache has been cleared because the publication status changed.'));
  }
}
