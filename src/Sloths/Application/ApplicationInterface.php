<?php

namespace Sloths\Application;
use Sloths\Application\Service\ServiceManager;
use Sloths\Http\RequestInterface;
use Sloths\Http\ResponseInterface;
use Sloths\Misc\ConfigurableInterface;
use Sloths\Observer\ObserverInterface;
use Sloths\Routing\Router;

/**
 * @property \Sloths\Application\Service\Authenticator          $authenticator
 * @property \Sloths\Application\Service\CacheManager           $cacheManager
 * @property \Sloths\Application\Service\Database               $database
 * @property \Sloths\Application\Service\DateTime               $dateTime
 * @property \Sloths\Application\Service\FlashMessage           $flashMessage
 * @property \Sloths\Application\Service\FlashSession           $flashSession
 * @property \Sloths\Application\Service\Mcrypt                 $mcrypt
 * @property \Sloths\Application\Service\Migrator               $migrator
 * @property \Sloths\Application\Service\Paginator              $paginator
 * @property \Sloths\Application\Service\Password               $password
 * @property \Sloths\Application\Service\Redirector             $redirector
 * @property \Sloths\Application\Service\Session                $session
 * @property \Sloths\Application\Service\Translator             $translator
 * @property \Sloths\Application\Service\Url                    $url
 * @property \Sloths\Application\Service\Validator              $validator
 * @property \Sloths\Application\Service\View                   $view
 * @property \Sloths\Application\Service\Slugify                $slugify
 * @property \Sloths\Misc\Parameters                            $params
 */
interface ApplicationInterface extends ObserverInterface, ConfigurableInterface
{
    const VERSION = '5.0.0';

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
     * @param string $directory
     * @return $this
     */
    public function setResourceDirectory($directory);

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
     * @param bool $full
     */
    public function getBaseUrl($full = true);

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