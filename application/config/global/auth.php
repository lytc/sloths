<?php

use Sloths\Authentication\Adapter\Callback;
use Application\Model\User;
use Sloths\Authentication\Result;

/* @var $this \Sloths\Application\Service\Authenticator */

$this->setAdapter(
    new Callback(function($adapter, $email, $password) {
        $user = User::all(['email' => $email])->first();

        if (!$user) {
            return Result::ERROR_IDENTITY_NOT_FOUND;
        }

        if (md5($password) !== $user->password) {
            return Result::ERROR_CREDENTIAL_INVALID;
        }

        return $user;
    })
);