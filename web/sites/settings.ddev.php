<?php

/**
 * @file
 * Default DDEV settings.
 */

$databases['default']['default'] = [
  'database' => $subsite_id,
  'username'  => getenv('DB_USER'),
  'password'  => getenv('DB_PASS'),
  'host'      => getenv('DB_HOST'),
  'port'      => getenv('DB_PORT'),
  'driver'    => getenv('DB_DRIVER'),
];

$databases[$subsite_id . '7']['default'] = [
  'database' => $subsite_id . '_legacy',
  'username' => getenv('DB_USER'),
  'password' => getenv('DB_PASS'),
  'prefix' => getenv('DB_PREFIX'),
  'host' => getenv('DB_HOST'),
  'port' => getenv('DB_PORT'),
  'namespace' => getenv('DB_NAMESPACE'),
  'driver' => getenv('DB_DRIVER'),
];

$databases['migrate']['default'] = $databases[$subsite_id . '7']['default'];

// Recommended setting for Drupal 10 only.
$settings['state_cache'] = TRUE;

// This will prevent Drupal from setting read-only permissions on sites/default.
$settings['skip_permissions_hardening'] = TRUE;

// This will ensure the site can only be accessed through the intended host
// names. Additional host patterns can be added for custom configurations.
$settings['trusted_host_patterns'] = ['.*'];

// Don't use Symfony's APCLoader. ddev includes APCu; Composer's APCu loader has
// better performance.
$settings['class_loader_auto_detect'] = FALSE;

// Set $settings['config_sync_directory'] if not set in settings.php.
if (empty($settings['config_sync_directory'])) {
  $settings['config_sync_directory'] = 'sites/default/files/sync';
}

// Assume all Lando sites should use 'local' config for devlopment.
$config['config_split.config_split.local']['status'] = TRUE;
$config['config_split.config_split.hosted']['status'] = FALSE;

// Override drupal/symfony_mailer default config to use Mailpit.
$config['symfony_mailer.settings']['default_transport'] = 'sendmail';
$config['symfony_mailer.mailer_transport.sendmail']['plugin'] = 'smtp';
$config['symfony_mailer.mailer_transport.sendmail']['configuration']['user'] = '';
$config['symfony_mailer.mailer_transport.sendmail']['configuration']['pass'] = '';
$config['symfony_mailer.mailer_transport.sendmail']['configuration']['host'] = 'localhost';
$config['symfony_mailer.mailer_transport.sendmail']['configuration']['port'] = '1025';

// Enable verbose logging for errors.
// https://www.drupal.org/forum/support/post-installation/2018-07-18/enable-drupal-8-backend-errorlogdebugging-mode
$config['system.logging']['error_level'] = 'verbose';
