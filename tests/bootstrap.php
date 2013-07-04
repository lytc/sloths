<?php

use Lazy\Db\Connection;

$loader = require_once __DIR__ . '/../vendor/autoload.php';

Connection::setEnv(Connection::ENV_TEST);
Connection::setDefaultConfig(array(
    'test' => array(
        'dsn' => 'mysql:host=localhost;dbname=lazy_db_test',
        'username' => 'root'
    )
));

Connection::getDefaultInstance()->exec(file_get_contents(__DIR__ . '/LazyTest/Db/fixtures/sample-database.sql'));