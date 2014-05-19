<?php

$application = $this;
$this->get('/bar', function() use ($application) {
    return $application;
});

$this->post('baz', function() {

});