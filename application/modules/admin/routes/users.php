<?php

use Application\Model\User;

$this->get('/', function() {
    $users = User::all();

    if ($q = trim($this->params->q)) {
        $users->where(function($select) use ($q) {
            $select
                ->or('name LIKE %?%', $q)
                ->or('email LIKE %?%', $q)
                ->or('phone LIKE %?%', $q)
            ;
        });
    }

    return $this->render('users/list', [
        'paginator' => $this->paginator->paginate($users)
    ]);
});

$this->get('/::id', function($id) {

    $user = User::first($id);
    $user || $this->notFound();

    return $this->render('users/view', ['user' => $user]);

});

$this->get('/new', function() {
    return $this->render('users/new');
});

$this->post('/', function() {

    $user = new User();

    $validator = $this->validator->create([
        'email'     => ['required', 'email', 'unique' => [$user, 'email']],
        'password'  => ['required', 'password'],
        'name'      => 'required',
        'birthday'  => ['date', 'before' => new DateTime()]
    ]);

    $data = $this->params->trim();

    if (!$validator->validate($data)) {
        return [
            'success'   => false,
            'messages'  => $validator->getMessages()
        ];
    }

    $data = $data->only('password name avatar phone address birthday');
    $data['password'] = md5($data['password']);

    return $this->user()->fromArray($data)->save();
});

$this->get('/::id/edit', function($id) {

    $user = User::first($id);
    $user || $this->notFound();

    return $this->render('users/edit', ['user' => $user]);

});

$this->put('/::id', function($id) {

    $user = User::first($id);
    $user || $this->notFound();

    $validator = $this->validator->create([
        'email'     => ['required', 'email', 'unique' => [$user, 'email']],
        'password'  => ['password'],
        'name'      => 'required',
        'birthday'  => ['date', 'before' => new DateTime()]
    ]);

    $data = $this->params->trim();

    if (!$validator->validate($data)) {
        return [
            'success'   => false,
            'messages'  => $validator->getMessages()
        ];
    }

    if ($data['password']) {
        $data['password'] = md5($data['password']);
        $data = $data->only('password name avatar phone address birthday');
    } else {
        $data = $data->only('name avatar phone address birthday');
    }

    return $user->fromArray($data)->save();
});

$this->delete('/::id', function($id) {

    $user = User::first($id);
    $user || $this->notFound();

    return $user->delete();

});