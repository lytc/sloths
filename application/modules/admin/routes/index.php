<?php

$this->get('/', function() {

//    $users = $this->database->table('users')->select()->limit(10)->remember('+5 seconds')->all();
//    var_dump($users);

    return $this->render('dashboard');

});