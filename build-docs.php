<?php

$root = __DIR__;
$buildDir = $root;
$pages = [
    'index',
    'docs/installation',
    'docs/system-requirements',
    'docs/application-structure',
    'docs/server-configuration',
    'docs/routing',
    'docs/request',
    'docs/response',

    'docs/databases/connection',
    'docs/databases/query-builder',

    'docs/orm/define-model',
    'docs/orm/model-overview',
    'docs/orm/model',
    'docs/orm/collection',
    'docs/orm/relationship-overview',
    'docs/orm/relationship-has-one',
    'docs/orm/relationship-has-many',
    'docs/orm/relationship-belongs-to',
    'docs/orm/relationship-has-many-through'
];


require_once $root . '/application/init.php';

# copy assets folder
echo "# Copying assets\n";
exec("cp -r $root/public/assets $buildDir");

$application = new \Application\Application();
error_reporting(E_ERROR);

foreach ($pages as $page) {
    echo "# Building: $page.html\n";

    $request = new \Sloths\Application\Service\Request([
        '_SERVER' => ['REQUEST_METHOD' => 'GET', 'PATH_INFO' => "/$page.html"]
    ]);

    ob_start();
    $application->setService('request', $request);
    $application->run();
    $content = ob_get_clean();

    $file = "$buildDir/$page.html";
    $dirName = pathinfo($file, PATHINFO_DIRNAME);

    file_exists($dirName) || mkdir($dirName, 0777, true);

    file_put_contents($file, $content);

}