<?php

namespace Sloths\Application\Service;

use Sloths\Application\Application;

class View extends \Sloths\View\View implements ServiceInterface
{
    use ServiceTrait;

    public function setApplication(Application $application)
    {
        if (!$this->directory) {
            $this->setDirectory($application->getDirectory() . '/views');
        }
        $this->application = $application;

        return $this;
    }
}