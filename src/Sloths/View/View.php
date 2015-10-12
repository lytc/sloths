<?php

namespace Sloths\View;

use Sloths\View\Helper\HelperInterface;

/**
 *
 * @method \Sloths\View\Helper\Assets assets
 */
class View
{
    /**
     * @var string
     */
    protected $extension = '.html.php';

    /**
     * @var bool
     */
    protected $layout = false;

    /**
     * @var array
     */
    protected $helperNamespaces = [
        'Sloths\View\Helper'
    ];

    /**
     * @var array
     */
    protected $helpers = [];

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var array
     */
    protected $variables = [];

    /**
     * @param string $extension
     * @return $this
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param $directory
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setDirectory($directory)
    {
        if (!is_dir($directory)) {
            throw new \InvalidArgumentException('Invalid directory: ' . $directory);
        }

        $this->directory = realpath($directory);

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
     * @param string|false $layout
     * @return $this
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * @param string $namespace
     * @return $this
     */
    public function addHelperNamespace($namespace)
    {
        $this->helperNamespaces[] = $namespace;
        return $this;
    }

    /**
     * @param array $helpers
     * @return $this
     */
    public function setHelpers(array $helpers)
    {
        foreach ($helpers as $name => $helper) {
            $this->addHelper($name, $helper);
        }

        return $this;
    }

    /**
     * @param $name
     * @param callable $helper
     * @return $this
     */
    public function addHelper($name, callable $helper)
    {
        $this->helpers[$name] = $helper;
        return $this;
    }

    /**
     * @param string $method
     * @param array $args
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call($method, $args)
    {
        if (isset($this->helpers[$method])) {
            return call_user_func_array($this->helpers[$method], $args);
        }

        foreach ($this->helperNamespaces as $namespace) {
            $helperClassName = $namespace . '\\' . ucfirst($method);

            if (class_exists($helperClassName)) {
                $helperClass = new $helperClassName();

                if ($helperClass instanceof HelperInterface) {
                    $helperClass->setView($this);
                }

                $this->helpers[$method] = $helperClass;
                return call_user_func_array($helperClass, $args);
            }
        }

        throw new \BadMethodCallException('Call to undefined helper ' . $method);
    }

    /**
     * @param array $variables
     * @return $this
     */
    public function setVariables(array $variables)
    {
        $this->variables = array_merge($this->variables, $variables);
        return $this;
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setVariable($name, $value)
    {
        $this->variables[$name] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasVariable($name)
    {
        return array_key_exists($name, $this->variables);
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getVariable($name)
    {
        return $this->hasVariable($name)? $this->variables[$name] : null;
    }

    /**
     * @param string $template
     * @param array $variables
     * @return string
     * @throws \InvalidArgumentException
     */
    public function render($template, array $variables = [])
    {
        if ('/' !== $template[0]) {
            $template = $this->getDirectory() . '/' . $template;
        }

        if (!pathinfo($template, PATHINFO_EXTENSION)) {
            $template = $template . $this->getExtension();
        }

        if (!is_file($template)) {
            throw new \InvalidArgumentException('Template file not found: ' . $template);
        }

        $this->setVariables($variables);

        $content = $this->_render($template, $this->variables);

        if ($this->layout) {
            $layoutView = clone $this;
            $layoutView->setLayout(false);

            $layoutView->helpers['content'] = function() use ($content) {
                return $content;
            };

            return $layoutView->render($this->layout, $this->variables);
        }

        return $content;
    }

    /**
     * @param string $__template__
     * @param array $variables
     * @return string
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    protected function _render($__template__, array $variables = [])
    {
        extract($variables);

        try {
            ob_start();
            include $__template__;
            $content = ob_get_clean();
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }

        return $content;
    }
}