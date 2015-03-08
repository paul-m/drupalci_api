<?php

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use DerAlex\Silex\YamlConfigServiceProvider;
use Silex\Application;

$app = new Application();

$app['root_path'] = __DIR__;

// Config parameters.
$config = $app['root_path'] . '/config/config.yml';
if (file_exists($config)) {
  $app->register(new YamlConfigServiceProvider($config));
}

// After-controller middleware.
$app->after(function (Request $request, Response $response) {
  // Make sure we wrap JSONP in callback if present.
  if ($response instanceof JsonResponse) {
    $callback = $request->get('callback', '');
    if($callback) {
      $response->setCallback($callback);
    }
  }
});

/**
 * Error handling.
 */
//$app->error(function (\Exception $e, $code) use ($app) {
//  error_log($e);
//  return "Something went wrong. Please contact the DrupalCI team.";
//});

$app['routes'] = $app->extend('routes', function (RouteCollection $routes, Application $app) {
  $loader = new YamlFileLoader(new FileLocator($app['root_path'] . '/config'));
  $collection = $loader->load('routes.yml');
  $routes->addCollection($collection);
  return $routes;
});

return $app;