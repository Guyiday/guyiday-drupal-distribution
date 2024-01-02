<?php

// This also includes settings.local.php and settings.ddev.php if these files exist.
require __DIR__ . '/../../../../includes/settings.php';

$settings['file_scan_ignore_directories'][] = 'full-installation-sandbox';
$settings['install_profile'] = 'burst_distribution';
