<?php

$this->view->setLayout('docs');

$this->get('/', function() {
    $this->redirectTo('/docs/installation.html');
});

$this->get('/?:section?/:doc.html', function($section, $doc) {
    $view = $this->view;
    $file = $section? "docs/$section/$doc" : "docs/$doc";

    $title = trim($doc);
    $title = explode('-', $title);
    $title = array_map('ucfirst', $title);
    $title = implode(' ', $title);

    if ($section) {
        $section = trim($section);
        $section = explode('-', $section);
        $section = array_map('ucfirst', $section);

        $title = implode(' ', $section) . ' - ' . $title;
    }

    $view->setFile($file);

    if (!file_exists($view->getFilePath())) {
        $this->notFound();
    }

    return $this->render([
        '_title' => 'Sloth Framework - ' . $title
    ]);
});