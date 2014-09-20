<?php

$applicationDirectory = realpath(__DIR__ . '/../');
define('APPLICATION_DIRECTORY', $applicationDirectory);

define('MODULE_SHARED_DIRECTORY', APPLICATION_DIRECTORY . '/modules/_shared');
$loader = require_once $applicationDirectory . '/vendor/autoload.php';


$loader->add('Sloths\\', $applicationDirectory . '/../src');

$moduleManager = new \Sloths\Application\ModuleManager();
$moduleManager
    ->setDirectory($applicationDirectory)

    ->add('content')
    ->add('admin', ['baseUrl' => '/admin/'])
    ->add('auth', ['baseUrl' => '/auth/'])
    ->add('account', ['baseUrl' => '/account/'])

//    ->setDefault('content')
;

$moduleManager->resolve(function($application) use ($applicationDirectory) {
    $application->setEnv(require $applicationDirectory . '/config/env.php');
})->run();