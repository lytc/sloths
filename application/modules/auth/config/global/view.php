<?php

/* @var $this \Sloths\Application\Service\View */

$this
    ->setLayout(MODULE_SHARED_DIRECTORY . '/views/_layouts/common')
    ->assets([
        'application' => [
            'extends' => 'common',
            'sources' => []
        ]
    ])
;