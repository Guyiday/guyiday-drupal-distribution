# Burst Drupal Distribution

Because we use Drupal 9 a lot, this makes all of our lives easier.

This distribution aims to remove the amount of boilerplate code we have in our projects. Less copy-pasting between projects, more re-using the same code.

## How to use

Create a composer.json, and make sure it contains at least the following:

```json
{
  "type": "project",
  "minimum-stability": "dev",
  "prefer-stable": true,
  "repositories": [
    {
      "type": "composer",
      "url": "https://packages.drupal.org/8"
    }
  ],
  "require": {},
  "extra": {
    "installer-paths": {
      "web/core": [
        "type:drupal-core"
      ],
      "web/libraries/{$name}": [
        "type:drupal-library"
      ],
      "web/modules/contrib/{$name}": [
        "type:drupal-module"
      ],
      "web/profiles/contrib/{$vendor}-{$name}": [
        "type:drupal-profile"
      ],
      "web/themes/contrib/{$name}": [
        "type:drupal-theme"
      ],
      "drush/contrib/{$name}": [
        "type:drupal-drush"
      ]
    },
    "enable-patching": true,
    "drupal-scaffold": {
      "locations": {
        "web-root": "web"
      }
    },
    "composer-exit-on-patch-failure": true
  },
  "config": {
    "sort-packages": true,
    "discard-changes": true,
    "platform": {
      "php": "7.4.999",
      "ext-curl": "7.4.999",
      "ext-gd": "7.4.999"
    }
  }
}
```

Note the following:
  - No drupal/core is required, it is already required by the Burst distribution.

Then, run `composer require burst/drupal-distribution`.

Now, the best step of all.. replace `/web/sites/default/settings.php` with the following:

```php
<?php

// This also includes settings.local.php and settings.ddev.php if these files exist.
require __DIR__ . '/../../profiles/contrib/burst-drupal-distribution/includes/settings.php';

```

Boom! This includes all configuration needed for Platform.sh, Lando, and probabily other services in the future.
