<?php

$buildDir = __DIR__  .'/build/docs';
$pages = [
    'index',
    'docs/installation',
    'docs/system-requirements',
    'docs/application-structure',
    'docs/server-configuration',
    'docs/routing',

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


require_once __DIR__ . '/docs/application/init.php';

# copy assets folder
echo "# Copying assets\n";
exec("cp -r public/assets $buildDir");

$application = new \Application\Application();

foreach ($pages as $page) {
    echo "# Building: $page.html\n";

    $request = new \Lazy\Http\Request([
        '_SERVER' => ['REQUEST_METHOD' => 'GET', 'PATH_INFO' => "/$page.html"]
    ]);

    ob_start();
    $application->response($request);
    $content = ob_get_clean();

    $file = "$buildDir/$page.html";
    $dirName = pathinfo($file, PATHINFO_DIRNAME);

    file_exists($dirName) || mkdir($dirName, 0777, true);

    file_put_contents($file, $content);

}