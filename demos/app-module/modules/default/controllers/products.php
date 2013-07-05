<?php

$this->get('/', function() {
    echo 'Product list page';
});

$this->get('/::id', function($id) {
    echo 'Product detail page for id ' . $id;
});