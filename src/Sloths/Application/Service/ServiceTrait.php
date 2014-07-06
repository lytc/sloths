<?php

namespace Sloths\Application\Service;

use Sloths\Application\Application;

trait ServiceTrait
{
    /**
     * @var Application
     */
    protected $application;

    /**
     * @param Application $application
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }
}