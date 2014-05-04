<?php

namespace Application;

class Application extends \Lazy\Application\Application
{
    public function __construct()
    {
        call_user_func_array('parent::__construct', func_get_args());
        require __DIR__ . '/boot.php';
    }
}