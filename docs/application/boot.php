<?php

$this->setPath(__DIR__);
$this->register('view', function() {
    \Lazy\View\View::addHelperNamespace('Application\View\Helper');

    $view = new \Lazy\View\View();

    $view
        ->setPath($this->getPath() . '/views')
        ->setLayout('default')
    ;

    return $view;
});