<?php

namespace Sloths\Application;

class Console
{
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function run()
    {

    }
}