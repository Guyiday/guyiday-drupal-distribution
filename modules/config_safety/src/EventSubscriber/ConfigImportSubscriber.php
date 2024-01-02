<?php

namespace Drupal\config_safety\EventSubscriber;

use Drupal\Core\Config\ConfigImporterEvent;
use Drupal\Core\Config\ConfigImportValidateEventSubscriberBase;

class ConfigImportSubscriber extends ConfigImportValidateEventSubscriberBase {

  /**
   * Checks that the configuration synchronization is valid.
   *
   * @param ConfigImporterEvent $event
   *   The config import event.
   */
  public function onConfigImporterValidate(ConfigImporterEvent $event) {
    $source = $event->getConfigImporter()->getStorageComparer()->getSourceStorage();

    if (!$source->exists('config_safety.schema_versions')) {
      return;
    }

    foreach ($source->read('config_safety.schema_versions') as $module => $update_version) {
      $current_update_version = (int) drupal_get_installed_schema_version($module);

      if ($current_update_version === SCHEMA_UNINSTALLED) {
        continue;
      }

      if ($update_version < $current_update_version) {
        $event->getConfigImporter()->logError("The current Drupal installation contains a newer version of $module then what's in the config you're trying to import. Did you forget to export configuration after updating modules?");
      }

      if ($update_version > $current_update_version) {
        $event->getConfigImporter()->logError("The current Drupal installation contains a older version of $module then what's in the config you're trying to import.");
      }
    }
  }
}
