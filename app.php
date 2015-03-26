<?php

require_once __DIR__ . '/vendor/autoload.php';

use API\Services\Jenkins;
use API\Services\Results;
use API\Services\Runner;
use DerAlex\Silex\YamlConfigServiceProvider;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RouteCollection;

$app = new Application();

$app['root_path'] = __DIR__;

// Config parameters.
$config = $app['root_path'] . '/config/config.yml';
if (file_exists($config)) {
  $app->register(new YamlConfigServiceProvider($config));
}

// Set up Jenkins service.
$jenkins_options = array_merge(
  [
    'host' => 'localhost',
    'port' => '80',
    'protocol' => 'http',
  ],
  $app['config']['jenkins']
);
$app['jenkins'] = $app->share(
  function ($app) {
    // @todo: Determine more/better parameters to inject here.
    $c = $app['config']['jenkins'];
    return new Jenkins($c['host'], $c['port'], $c['protocol']);
  }
);
unset($jenkins_options);

// Set up Results service.
// @todo: Set up config items for this service.
$app['results'] = $app->share(
  function ($app) {
    return new Results();
  }
);

// Set up Runner service.
$app['runner'] = $app->share(
  function ($app) {
    return Runner::create($app);
  }
);

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
  if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
    $data = json_decode($request->getContent(), TRUE);
    $request->request->replace(is_array($data) ? $data : array());
  }
});

// Make sure we wrap JSONP in a callback if present.
$app->after(function (Request $request, Response $response) {
  if ($response instanceof JsonResponse) {
    $callback = $request->get('callback', '');
    if ($callback) {
      $response->setCallback($callback);
    }
  }
});

$app['routes'] = $app->extend('routes', function (RouteCollection $routes, Application $app) {
  $loader = new YamlFileLoader(new FileLocator($app['root_path'] . '/config'));
  $collection = $loader->load('routes.yml');
  $routes->addCollection($collection);
  return $routes;
});

return $app;
