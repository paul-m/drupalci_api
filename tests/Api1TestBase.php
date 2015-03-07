<?php

/**
 * @file
 * A base test for all our API tests.
 */

namespace API\Tests;

use Silex\WebTestCase;
use DerAlex\Silex\YamlConfigServiceProvider;

/**
 * Base test class for API tests.
 *
 * We do this so we only have one place to manage the path to the app file
 * in createApplication().
 */
class Api1TestBase extends WebTestCase {

  protected function apiPrefix() {
    return 'drupalci/api/1';
  }

  public function createApplication() {
    $app = require __DIR__ . '/../app.php';
    $app['debug'] = TRUE;

    // Set up our test environment if it exists.
    $test_config_path = $app['root_path'] . '/tests/config.yml';
    if (file_exists($test_config_path)) {
      $app->register(new YamlConfigServiceProvider($test_config_path));
    }

    return $app;
  }
}
