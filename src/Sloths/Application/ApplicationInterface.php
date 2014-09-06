<?php

namespace Sloths\Application;
use Sloths\Application\Service\ServiceManager;
use Sloths\Http\RequestInterface;
use Sloths\Http\ResponseInterface;
use Sloths\Misc\ConfigurableInterface;
use Sloths\Observer\ObserverInterface;
use Sloths\Routing\Router;

interface ApplicationInterface extends ObserverInterface, ConfigurableInterface
{
    /**
     * @return string
     */
    public function getEnv();

    /**
     * @param string $directory
     * @throws \InvalidArgumentException
     */
    public function setDirectory($directory);

    /**
     * @return string
     */
    public function getDirectory();

    /**
     * @param string $baseUrl
     * @return $this
     */
    public function setBaseUrl($baseUrl);

    /**
     * @return string
     */
    public function getBaseUrl();

    /**
     * @param string $name
     * @return string
     */
    public function getPath($name);

    /**
     * @param \Sloths\Http\RequestInterface $request
     * @return $this
     */
    public function setRequest(RequestInterface $request);

    /**
     * @return \Sloths\Http\RequestInterface
     */
    public function getRequest();

    /**
     * @param \Sloths\Http\ResponseInterface $response
     * @return $this
     */
    public function setResponse(ResponseInterface $response);

    /**
     * @return \Sloths\Http\ResponseInterface
     */
    public function getResponse();

    /**
     * @param Router $router
     * @return mixed
     */
    public function setRouter(Router $router);

    /**
     * @return \Sloths\Routing\Router
     */
    public function getRouter();

    /**
     * @param ServiceManager $manager
     * @return $this
     */
    public function setServiceManager(ServiceManager $manager);

    /**
     * @return ServiceManager
     */
    public function getServiceManager();

    /**
     * @param ConfigLoader $configLoaderLoader
     * @return mixed
     */
    public function setConfigLoader(ConfigLoader $configLoaderLoader);

    /**
     * @return \Sloths\Application\ConfigLoader
     */
    public function getConfigLoader();

    /**
     * @event before[$this]
     * @event after[$this]
     *
     * @return $this
     */
    public function run();
}