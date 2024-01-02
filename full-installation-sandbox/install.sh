mv ./web/sites/default/settings.php ./_settings.php
rm -rf web vendor composer.lock
mkdir -p ./web/sites/default
mv ./_settings.php ./web/sites/default/settings.php
rsync -a .. .sl --exclude .git --exclude full-installation-sandbox
COMPOSER_MEMORY_LIMIT=-1 composer install
rm -rf composer.lock .sl ./web/profiles/contrib/burst-drupal-distribution
ln -s ../../../../ web/profiles/contrib/burst-drupal-distribution
