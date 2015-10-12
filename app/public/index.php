<?php

/* @var $application Sloths\Application\Application */

error_reporting(E_ALL);

$application = require __DIR__ . '/../src/application.php';
$application->addEventListener('boot', function(\Sloths\Observer\Event $event, \Sloths\Application\Application $application) {
    $request = $application->getRequest();
    $requestPath = $request->getPath();

    $basePath = '/' . current(explode('/', ltrim($requestPath, '/')));

    $map = [
        '/admin'    => 'admin',
        '/auth'     => 'auth'
    ];

    if (isset($map[$basePath])) {
        $application
            ->setResourceDirectory($map[$basePath])
            ->setBaseUrl($basePath)
        ;
    }
});

$application->run();