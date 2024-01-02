<?php

$profile_to_install = 'burst_distribution';
$profile_to_remove = \Drupal::installProfile();

\Drupal::state()->delete('system.profile.files');

$extension_config = \Drupal::configFactory()->getEditable('core.extension');
$extension_config->set('profile', $profile_to_install)->save();

drupal_flush_all_caches();

$extension_config->clear("module.{$profile_to_remove}")->save();
$extension_config->set("module.$profile_to_install", 1000)->save();

\Drupal::keyValue('system.schema')->delete($profile_to_remove);
\Drupal::keyValue('system.schema')->set($profile_to_install, 8000);

drupal_flush_all_caches();
