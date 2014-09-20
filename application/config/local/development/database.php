<?php

/* @var $this \Sloths\Application\Service\ConnectionManager */

$this->setConnection(new \Sloths\Db\Connection(
    'mysql:host=127.0.0.1;dbname=sloths-application', // dsn
    'root', // username
    '', // password
    [ // options
        PDO::MYSQL_ATTR_INIT_COMMAND    => "SET NAMES 'utf8'"
    ]
));