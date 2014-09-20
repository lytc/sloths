<?php

$this->get('/', function() {
    if ($this->auth->exists()) {
        return $this->render('signed-in-message');
    }

    return $this->render('sign-in');
});

$this->post('/', function() {
    $email = $this->params->email;
    $password = $this->params->password;

    $result = $this->auth->authenticate($email, $password);

    if ($result->isSuccess()) {

        if ($returnUrl = $this->params->returnUrl) {
            return $this->redirector->to($returnUrl);
        }

        $this->redirector->to($this->url);

    } else {
        switch ($result->getCode()) {
            case $result::ERROR_IDENTITY_NOT_FOUND:
                $message = 'Invalid email address';
                break;

            case $result::ERROR_CREDENTIAL_INVALID:
                $message = 'Invalid password';
                break;

            default:
                $message = 'Authentication fail';
        }

        $this->message->error($message);
        return $this->redirector->to($this->url);
    }
});

$this->delete('/', function() {
    $this->auth->clear();
    return $this->redirector->to($this->url);
});