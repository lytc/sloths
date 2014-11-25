<?php

namespace Sloths\Application;

use Sloths\Http\Request;
use Sloths\Http\RequestInterface;
use Sloths\Observer\ObserverTrait;

class ModuleManager
{
    use ObserverTrait;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $modules = [];

    /**
     * @var array
     */
    protected $moduleInstances = [];

    /**
     * @var string
     */
    protected $default;

    /**
     * @param string $directory
     * @return $this
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
        return $this;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @param RequestInterface $request
     * @return $this
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return Request|RequestInterface
     */
    public function getRequest()
    {
        if (!$this->request) {
            $this->request = new Request();
        }

        return $this->request;
    }

    /**
     * @param string $name
     * @param string|array $condition
     * @param string|\Sloths\Application\ApplicationInterface $class $class
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function add($name, $condition = [], $class = 'Sloths\Application\Application')
    {
        if (!is_string($class) && !is_callable($class) && !$class instanceof ApplicationInterface) {
            throw new \InvalidArgumentException(sprintf(
                'Class must be instanceof \Sloths\Application\ApplicationInterface or string of class name or callable. %s given',
                gettype($class)
            ));
        }

        $this->modules[$name] = [
            'condition' => is_array($condition)? $condition : ['baseUrl' => $condition],
            'class'     => $class
        ];

        if (!$condition && !$this->default) {
            $this->setDefault($name);
        }

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setDefault($name)
    {
        $this->default = $name;
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->modules[$name]);
    }

    /**
     * @param $name
     * @return mixed
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException('Undefined module: '. $name);
        }

        if (isset($this->moduleInstances[$name])) {
            return $this->moduleInstances[$name];
        }

        $options = $this->modules[$name];
        $instance = $options['class'];

        if (!$instance instanceof ApplicationInterface) {
            if (is_string($instance)) {
                $instance = new $instance($this);
            } else {
                $instance = call_user_func($instance);
            }

            if (!$instance instanceof ApplicationInterface) {
                throw new \UnexpectedValueException(sprintf(
                    'Module must be instanceof \Sloths\Application\ApplicationInterface. %s given',
                    gettype($instance)
                ));
            }
        }

        if (!$instance->getDirectory()) {
            $instance->setDirectory($this->getDirectory() . '/modules/' . $name);
        }

        if (isset($options['condition']['baseUrl'])) {
            $instance->setBaseUrl($options['condition']['baseUrl']);
        }

        $instance->getConfigLoader()->addDirectories([
            $this->directory . '/config',
            $this->directory . '/modules/' . $name . '/config'
        ]);

        $this->moduleInstances[$name] = $instance;

        $this->triggerEventListener('create', [$instance, $this]);

        return $instance;
    }

    /**
     * @param array $conditions
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function validate(array $conditions)
    {
        foreach ($conditions as $type => $value) {
            if (is_callable($value)) {
                if (!call_user_func($value, $this->getRequest())) {
                    return false;
                }
            } else {
                $validator = 'validate' . ucfirst($type);

                if (!method_exists($this, $validator)) {
                    throw new \InvalidArgumentException('Undefined validator: ' . $type);
                }

                if (!$this->$validator($value)) {
                    return false;
                }
            }

        }

        return true;
    }

    /**
     * @param string $baseUrl
     * @return bool
     */
    public function validateBaseUrl($baseUrl)
    {
        $request = $this->getRequest();

        if (!preg_match('/^https?:\/\//', $baseUrl)) {}

        $components = parse_url($baseUrl);

        if (isset($components['scheme']) && $components['scheme'] != $request->getScheme()) {
            return false;
        }

        if (isset($components['host']) && $components['host'] != $request->getHost()) {
            return false;
        }

        if (isset($components['port']) && $components['port'] != $request->getPort()) {
            return false;
        }

        if (isset($components['path'])) {
            $path = rtrim($components['path'], '/');
            $path || $path = '/';

            $requestPath = $request->getPath() . '/';
            return $path == $requestPath || (substr($requestPath, 0, strlen($path)) == $path);
        }

        return true;
    }

    /**
     * @param string $value
     * @return bool
     */
    public function validateBaseUrlRegex($value)
    {
        return !!preg_match($value, $this->getRequest()->getUrl(true));
    }

    /**
     * @param string $value
     * @return bool
     */
    public function validateHost($value)
    {
        return $this->getRequest()->getHost() == $value;
    }

    /**
     * @param string $value
     * @return bool
     */
    public function validateHostRegex($value)
    {
        return !!preg_match($value, $this->getRequest()->getHost(true));
    }

    /**
     * @param callable $callback
     * @return ApplicationInterface
     * @throws \RuntimeException
     */
    public function resolve(callable $callback = null)
    {
        $module = null;

        foreach ($this->modules as $name => $options) {
            $conditions = $options['condition'];

            if (!$conditions) {
                continue;
            }

            if ($this->validate($conditions)) {
                $module = $name;
            }
        }

        if (!$module) {
            if (!$this->default) {
                throw new \RuntimeException('No module found. A default module is required');
            }

            $module = $this->default;
        }

        $instance = $this->get($module);

        if ($callback) {
            call_user_func($callback, $instance);
        }

        return $instance;
    }
}