<?php

/* @var $this \Sloths\Application\Service\CacheManager */

$storage = new \Sloths\Cache\Storage\File();
$storage->setDirectory(APPLICATION_DIRECTORY . '/storage/cache');

//$memcached = new Memcached();
//$memcached->addServer('127.0.0.1', 11211);
//$storage = new \Sloths\Cache\Storage\Memcached();
//$storage->setMemcachedResource($memcached);

$this->setStorage($storage);