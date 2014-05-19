<?php

namespace Sloths\Application\Service;

use Sloths\Application\Application;

interface ServiceInterface
{
    public function setApplication(Application $application);
    public function getApplication();
}