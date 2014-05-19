<?php

require_once __DIR__ . '/../application/init.php';

$application = new \Application\Application('/sloths');
$application->run();