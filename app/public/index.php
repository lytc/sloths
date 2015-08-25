<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$moduleManager = new \Sloths\Application\ModuleManager();

$moduleManager
    ->setDirectory(__DIR__ . '/../src')
    ->add('content', '/')
    ->add('admin', '/admin')
;

$application = $moduleManager->resolve(function($application) {

});

$application->run();