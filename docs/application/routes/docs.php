<?php

$this->view->setLayout('docs');

$this->get('/', function() {
    $this->redirectTo('/docs/installation.html');
});

$this->get('/?:section?/:doc.html', function($section, $doc) {
    $view = $this->view;
    $file = "docs/$section";
    if ($doc) {
        $file .= "/$doc";
    }
    $view->setFile($file);

    if (!file_exists($view->getFilePath())) {
        $this->notFound();
    }

    return $this->render();
});