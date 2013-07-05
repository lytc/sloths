<?php
use Lazy\Application\Application;


require_once __DIR__ . '/../../../vendor/autoload.php';

$app = new Application(__DIR__ . '/../');
$app->run();