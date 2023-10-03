<?php

// @codingStandardsIgnoreFile

// Extract the directory name for multi site identification.
$subsite_id = basename(__DIR__);

include $app_root . '/sites/site.settings.php';

/**
 * Private file path:
 *
 * A local file system path where private files will be stored. This directory
 * must be absolute, outside of the Drupal installation directory and not
 * accessible over the web.
 *
 * Note: Caches need to be cleared when this value is changed to make the
 * private:// stream wrapper available to the system.
 *
 * See https://www.drupal.org/documentation/modules/file for more information
 * about securing private files.
 */
$settings['file_private_path'] = $app_root . '/../private/nisra';

