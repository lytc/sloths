#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';
$tool = new \Lazy\Tool(isset($argv[1])? $argv[1] : null);
$tool->run();