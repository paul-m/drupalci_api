<?php

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use DerAlex\Silex\YamlConfigServiceProvider;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Tobiassjosten\Silex\ResponsibleServiceProvider;

$app = new Application();

$app['root_path'] = __DIR__;

// Config parameters.
$config = $app['root_path'] . '/config/config.yml';
if (file_exists($config)) {
  $app->register(new YamlConfigServiceProvider($config));
}

$db_options = array_merge(
  [
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/drupalci_api.sqlite',
  ],
  $app['config']['db.options']
);

$app->register(new DoctrineServiceProvider(), ['db.options' => $db_options]);
unset($db_options);

$app->register(new DoctrineOrmServiceProvider(), array(
  "orm.proxies_dir" => __DIR__ . '/proxies',
  "orm.em.options" => array(
    "mappings" => array(
      array(
        "type" => "annotation",
        "namespace" => "API\Entities",
        "path" => __DIR__ . "/src/Entities",
      ),
    ),
  ),
));

$app->before(function (Request $request) {
  if (0 === strpos($request->request->headers->get('Content-Type'), 'application/json')) {
    $data = json_decode($request->getContent(), TRUE);
    $request->request->replace(is_array($data) ? $data : array());
  }
});

// After-controller middleware.
/*$app->after(function (Request $request, Response $response) {
  // Make sure we wrap JSONP in callback if present.
  if ($response instanceof JsonResponse) {
    $callback = $request->get('callback', '');
    if ($callback) {
      $response->setCallback($callback);
    }
  }
});*/

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
