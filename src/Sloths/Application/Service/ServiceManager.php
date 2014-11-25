<?php

namespace Sloths\Application\Service;

use Sloths\Application\ApplicationInterface;

class ServiceManager
{
    /**
     * @var ApplicationInterface
     */
    protected $application;

    /**
     * @var array
     */
    protected $services = [];

    /**
     * @var array
     */
    protected $initializedServices = [];

    /**
     * @param ApplicationInterface $application
     */
    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * @param array $services
     * @return $this
     */
    public function setServices(array $services)
    {
        foreach ($services as $name => $service) {
            $this->add($name, $service);
        }

        return $this;
    }

    /**
     * @param $name
     * @param $service
     * @return $this
     */
    public function add($name, $service)
    {
        $this->services[$name] = $service;
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->services[$name]);
    }

    /**
     * @param string $name
     * @return \Sloths\Application\ServiceInterface
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function get($name)
    {
        if (isset($this->initializedServices[$name])) {
            return $this->initializedServices[$name];
        }

        if (!$this->has($name)) {
            throw new \InvalidArgumentException('Undefined service: ' . $name);
        }

        $service = $this->services[$name];

        if (is_string($service)) {
            $service = new $service();
        } elseif (is_callable($service)) {
            $service = call_user_func($service);
        }

        if (!$service instanceof ServiceInterface) {
            $message = 'Service must be instance of \Sloths\Application\ServiceInterface. ';

            if (is_object($service) && $className = get_class($service)) {
                $message .= sprintf('Instance of %s given', $className);
            } else {
                $message .= gettype($service) . ' given';
            }

            throw new \UnexpectedValueException($message);
        }

        $service->setName($name);

        if ($service->getApplication() != $this->application) {
            $service->setApplication($this->application);
        }

        $this->initializedServices[$name] = $service;

        return $service;
    }
}