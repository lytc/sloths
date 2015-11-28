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
    protected $services = [
        'authenticator'     => 'Sloths\Application\Service\Authenticator',
        'cacheManager'      => 'Sloths\Application\Service\CacheManager',
        'database'          => 'Sloths\Application\Service\Database',
        'dateTime'          => 'Sloths\Application\Service\DateTime',
        'flashMessage'      => 'Sloths\Application\Service\FlashMessage',
        'flashSession'      => 'Sloths\Application\Service\FlashSession',
        'mcrypt'            => 'Sloths\Application\Service\Mcrypt',
        'migrator'          => 'Sloths\Application\Service\Migrator',
        'paginator'         => 'Sloths\Application\Service\Paginator',
        'password'          => 'Sloths\Application\Service\Password',
        'redirector'        => 'Sloths\Application\Service\Redirector',
        'session'           => 'Sloths\Application\Service\Session',
        'translator'        => 'Sloths\Application\Service\Translator',
        'url'               => 'Sloths\Application\Service\Url',
        'validator'         => 'Sloths\Application\Service\Validator',
        'view'              => 'Sloths\Application\Service\View',
        'slugify'           => 'Sloths\Application\Service\Slugify',
    ];

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
     * @return array
     */
    public function getAll()
    {
        return array_keys($this->services);
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