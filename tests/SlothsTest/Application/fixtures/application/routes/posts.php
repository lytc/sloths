<?php

$this->get('/', function() {
    return 'GET /posts';
});

$this->post('/', function() {
    return 'POST /posts';
});