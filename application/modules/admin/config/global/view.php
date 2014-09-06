<?php

/* @var $this \Sloths\Application\Service\View */

$this
    ->setLayout($this->getDirectory() . '/_layouts/default')
    ->assets([
        'application' => [
            'extends' => 'common',
            'sources' => [
                'stylesheets/application.css'
            ]
        ],
        'posts' => [
            'extends' => ['html-editor', 'application'],
        ]
    ])->setBaseUrl('/assets/modules/admin')
;