<?php

use Lazy\Application\Module;

require_once __DIR__ . '/../../../vendor/autoload.php';

define('MODULE_PATH', __DIR__ . '/../modules');

$appModule = new Module();

$appModule->add('/', function() {
    require_once MODULE_PATH . '/default/MyApp.php';
    return new \Demo\MyApp(MODULE_PATH . '/default');
});

$appModule->add('/admin', function() {
    require_once MODULE_PATH . '/admin/Admin.php';
    return new \Demo\Admin(MODULE_PATH . '/admin');
});

$appModule->run();