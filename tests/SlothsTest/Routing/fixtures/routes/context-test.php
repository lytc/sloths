<?php

$context = $this;
$this->get('/', function() use ($context) {
    return $context;
});