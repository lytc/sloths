<?php

use Lazy\Application\Module;

define('ROOT', realpath(__DIR__ . '/..'));
define('APPLICATION_PATH', ROOT . '/application');
define('MODULE_PATH', APPLICATION_PATH . '/modules');

require_once ROOT . '/vendor/autoload.php';


$appModule = new Module();

$appModule->add('/', function() {
    require_once MODULE_PATH . '/default/Default.php';
    return new \Demo\DefaultApplication(MODULE_PATH . '/default');
});

$appModule->add('/admin', function() {
    require_once MODULE_PATH . '/admin/Admin.php';
    return new \Demo\AdminApplication(MODULE_PATH . '/admin');
});

$appModule->run();