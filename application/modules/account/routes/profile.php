<?php

$this->get('/', function() {
    return $this->render('profile/edit');
});

$this->post('/', function() {
    $validator = $this->validator->create([
        'currentPassword' => ['required', function($password) {
            return md5($password) == $this->user()->password;
        }],
        'password' => function($password) {
                $len = strlen($password);

                if ($len >= 8                               // at least 8 characters
                    && preg_match('/[a-z]/', $password)     // lowercase
                    && preg_match('/[A-Z]/', $password)     // uppercase
                    && preg_match('/[0-9]/', $password)     // number
                    && preg_match('/[^\w]/', $password)     // symbol
                ) {
                    return true;
                }

                return 'must be at least 8 characters.Must contains lowercase (a-z), uppercase (A-Z), number (0-9) and symbol';
            },
        'name' => 'required',
        'birthday' => ['date', 'before' => new DateTime()]
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

    $this->user()->fromArray($data)->save();

    return true;
});