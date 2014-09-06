<?php

namespace Sloths\Application\Service;

use Sloths\Application\ApplicationInterface;
use Sloths\Misc\ConfigurableInterface;

interface ServiceInterface extends ConfigurableInterface
{
    /**
     * @param ApplicationInterface $application
     * @return $this
     */
    public function setApplication(ApplicationInterface $application);

    /**
     * @return ApplicationInterface
     */
    public function getApplication();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();
}