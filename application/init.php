<?php

error_reporting(E_ALL);
define('ROOT', realpath(__DIR__ . '/../'));
define('APPLICATION_ENV', getenv('APPLICATION_ENV')?: 'production');

require_once ROOT . '/vendor/autoload.php';