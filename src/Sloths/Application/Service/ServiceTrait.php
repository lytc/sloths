<?php

namespace Sloths\Application\Service;
use Sloths\Application\ApplicationInterface;
use Sloths\Misc\ConfigurableTrait;

trait ServiceTrait
{
    use ConfigurableTrait;

    /**
     * @var ApplicationInterface
     */
    protected $application;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param ApplicationInterface $application
     * @return $this
     */
    public function setApplication(ApplicationInterface $application)
    {
        $needReboot = $application !== $this->getApplication();
        $this->application = $application;

        if ($needReboot) {
            $this->boot();
            $application->getConfigLoader()->apply($this->getName(), $this);
        }

        return $this;
    }

    /**
     * @return ApplicationInterface
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function boot()
    {
        return $this;
    }
}