<?php

/* @var $this \Sloths\Application\Service\View */

$this
    ->setLayout($this->getDirectory() . '/_layouts/default')
    ->assets([
        'application' => [
            'extends' => 'common',
            'sources' => []
        ]
    ])
;