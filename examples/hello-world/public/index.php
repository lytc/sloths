<?php
use Lazy\Application\Application;

define('ROOT', realpath(__DIR__ . '/..'));
require_once ROOT . '/vendor/autoload.php';

$app = new Application(ROOT);
$app->run();