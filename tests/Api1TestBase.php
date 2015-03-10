<?php

/**
 * @file
 * A base test for all our API tests.
 */

namespace API\Tests;

use Silex\WebTestCase;
use DerAlex\Silex\YamlConfigServiceProvider;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use API\Tests\Fixtures\APIFixture;

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

    // Load the fixture and persist it to the database.
    $loader = new Loader();
    $loader->addFixture(new APIFixture());
    $executor = new ORMExecutor($app['orm.em'], new ORMPurger());
    $executor->execute($loader->getFixtures(), FALSE);

    return $app;
  }

}
