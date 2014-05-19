<?php

namespace Sloths\Application\Service;

use Sloths\Application\Application;

class Router extends \Sloths\Routing\Router implements ServiceInterface
{
    use ServiceTrait;

    public function setApplication(Application $application)
    {
        $this->setContext($application);

        if (!$this->directory) {
            $this->setDirectory($application->getDirectory() . '/routes');
        }

        $this->application = $application;
        return $this;
    }
}