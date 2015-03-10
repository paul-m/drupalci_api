<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

$app = include_once __DIR__ . '/../app.php';

return ConsoleRunner::createHelperSet($app['orm.em']);
