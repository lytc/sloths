<?php

use Application\Model\Post;

$this->get('/', function() {

    $posts = Post::all()->orderBy('created_time DESC');

    return $this->render('posts/list', [
        'paginator' => $this->paginator->paginate($posts)
    ]);

});

$this->get('/new', function() {

    return $this->render('posts/new');

});

$this->post('/', function() {

    $validator = $this->validator->create([
        'title' => 'required'
    ]);

    $data = $this->params->only('title thumbnail summary content')->trim();

    if (!$validator->validate($data)) {
        return [
            'success' => false,
            'messages' => $validator->getMessages()
        ];
    }

    $post = Post::create($data);
    $post->Creator = $this->user();

    return $post->save();

});