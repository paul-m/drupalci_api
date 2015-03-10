Testing DrupalCI API
===

The tests for DrupalCI API are written in the PHPUnit framework. Doctrine fixtures and Symfony2's BrowserKit Client for web-based testing.

To run the tests:

    $ cd drupalci_api
    $ composer install --dev
    $ ./bin/doctrine orm:schema:drop --force
    $ ./bin/doctrine orm:schema:create
    $ ./bin/phpunit

To write tests, you can:

* Subclass `API\Tests\Api1TestBase`. This is recommended if you are testing the API.

* Subclass `Silex\WebTestBase` and supply your own `createApplication()` method. This is recommended if you are writing other web-based tests, or need access to an `Application` object without the fixtures for the API tests.

* Just write a normal PHPUnit test based on `PHPUnit_Framework_TestCase`.
