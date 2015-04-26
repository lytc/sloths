<?php

$this->get('/', function() {
    return 'GET /foo/bar/baz/qux';
});

$this->get('/qot', function() {
    return 'GET /foo/bar/baz/qux/qot';
});