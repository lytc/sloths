<?php

$this->get('/', function() {
//    $user = \Application\Model\User::find()->limit(10)->toArray();
    return $this->render('home');
});