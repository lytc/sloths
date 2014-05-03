<?php
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';
//
//$connection = new \Lazy\Db\Connection('mysql:host=localhost;dbname=lazy-framework', 'root', '');
//\Lazy\Db\Model\AbstractModel::setDefaultConnection($connection);
//
//# generate models
//$tables = \Lazy\Db\Model\AbstractModel::getDefaultConnection()->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
//
//foreach ($tables as $table) {
//    $generator = \Lazy\Db\Model\Generator::fromTable($table, 'LazyTest\Db\Model\DbTable', \Lazy\Db\Model\AbstractModel::getDefaultConnection());
//    $generator->setDirectory(__DIR__);
//    $generator->write();
//}