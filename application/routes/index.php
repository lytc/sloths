<?php

$this->get('/', function() {
    $this->redirectTo('/index.html');
});

$this->get('/index.html', function() {
    return $this->render('home');
});