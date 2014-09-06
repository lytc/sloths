<?php

/* @var $this \Sloths\Application\Service\Migrator */

$this
    ->setDatabase($this->getApplication()->database)
    ->setDirectory($this->getApplication()->getPath('migrations'))
    ->setNamespace('Application\Migration')
;