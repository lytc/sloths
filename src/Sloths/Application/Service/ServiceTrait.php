<?php

namespace Sloths\Application\Service;

use Sloths\Application\Application;

trait ServiceTrait
{
    protected $application;

    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    public function getApplication()
    {
        return $this->application;
    }
}