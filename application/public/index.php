<?php

$projectDirectory = realpath(__DIR__ . '/../');

define('MODULE_SHARED_DIRECTORY', $projectDirectory . '/modules/_shared');
$loader = require_once $projectDirectory . '/vendor/autoload.php';


$loader->add('Sloths\\', $projectDirectory . '/../src');

$moduleManager = new \Sloths\Application\ModuleManager();
$moduleManager
    ->setDirectory($projectDirectory)

    ->add('content')
    ->add('admin', ['baseUrl' => '/admin/'])
    ->add('auth', ['baseUrl' => '/auth/'])
    ->add('account', ['baseUrl' => '/account/'])

//    ->setDefault('content')
;

$moduleManager->resolve(function($application) use ($projectDirectory) {
    $application->setEnv(require $projectDirectory . '/config/env.php');
})->run();