<?php

$this->get('/posts', function() {
    var_dump($this->getConfig()); exit;
    var_dump($this->isXhr()); exit;
    $this->responseHeader('foo', 'bar');
    return $this->param('author');
    return $this->params->author;
});