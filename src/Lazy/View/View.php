<?php
namespace Lazy\View;

use Lazy\View\Exception\Exception;
use Lazy\Util\String;


class View
{
    protected $path;
    protected $layoutPath;
    protected $extension = 'php';
    protected $layout = null;
    protected $template;
    protected $variables = [];
    protected $helpers = [];
    protected $config = [];
    protected static $helperPaths = [];

    public static function helperPaths(array $path = [])
    {
        if ($path) {
            static::$helperPaths = array_merge(static::$helperPaths, $path);
        }

        return static::$helperPaths;
    }

    public function __construct(array $config = [])
    {
        foreach ($config as $name => $value) {
            if (method_exists($this, $name)) {
                $this->{$name}($value);
            } else {
                $this->config[$name] = $value;
            }
        }
    }

    public function __call($method, $args)
    {
        if (!isset($this->helpers[$method])) {
            $className = String::camelize(true, $method);

            # find from helper class
            $helperClassName = __NAMESPACE__ . '\\Helper\\' . $className;

            if (!class_exists($helperClassName)) { // find from custom helper
                $helperPaths = array_merge(static::helperPaths(), [$this->path . '/helpers']);
                foreach ($helperPaths as $path) {
                    $helperFile = $path . '/' . $className . '.php';
                    if (file_exists($helperFile)) {
                        require_once $helperFile;
                    }
                }
            }

            if (class_exists($helperClassName)) {
                $helperInstance = new $helperClassName($this);

                if (isset($this->config['helpers'][$method])) {
                    foreach ($this->config['helpers'][$method] as $k => $v) {
                        if (method_exists($helperInstance, $k)) {
                            $helperInstance->{$k}($v);
                        }
                    }
                }

                $this->helpers[$method] = $helperInstance;
            }
        }

        if (isset($this->helpers[$method])) {
            $helper = $this->helpers[$method];
            if ($helper instanceof \Closure) {
                $callable = $helper;
            } else {
                $callable = [$helper, $method];
            }
            return call_user_func_array($callable, $args);
        }

        throw new Exception("Call undefined method $method");
    }

    public function path($path = null)
    {
        if (!func_num_args()) {
            return $this->path;
        }
        $this->path = $path;
        return $this;
    }

    public function layoutPath($path = null)
    {
        if (!func_num_args()) {
            if (null === $this->layoutPath) {
                return $this->path() . '/layouts';
            }

            return $this->layoutPath;
        }

        $this->layoutPath = $path;
        return $this;
    }

    public function layout($layout = null)
    {
        if (!func_num_args()) {
            if ($this->layout) {
                $layoutFile = $this->layout;
                if (!pathinfo($layoutFile, PATHINFO_EXTENSION)) {
                    $layoutFile .= '.' . $this->extension;
                }

                if (!file_exists($layoutFile)) {
                    $layoutFile = $this->layoutPath() . '/' . $layoutFile;
                }

                return $layoutFile;
            }
            return;
        }

        $this->layout = $layout;
        return $this;
    }

    public function template($template = null)
    {
        if (!func_num_args()) {
            if ($this->template) {
                $templateFile = $this->template;
                if (!pathinfo($templateFile, PATHINFO_EXTENSION)) {
                    $templateFile .= '.' . $this->extension;
                }

                if (!file_exists($templateFile)) {
                    $templateFile = $this->path() . '/' . $templateFile;
                }

                return $templateFile;
            }
            return;
        }

        $this->template = $template;
        return $this;
    }

    public function variables($name = null, $value = null)
    {
        switch (func_num_args()) {
            case 0: return $this->variables;

            case 1:
                if (is_array($name)) {
                    $this->variables = array_merge($this->variables, $name);
                    return $this;
                }

                return $this->variables[$name];

            default:
                $this->variables[$name] = $value;
                return $this;
        }
    }

    protected function _render($templateFile)
    {
        if (!file_exists($templateFile)) {
            throw new Exception("View file not found: $templateFile");
        }

        $errorReporting = error_reporting();
        error_reporting(E_ALL);

        extract($this->variables);
        ob_start();
        try {
            include $templateFile;
            $result = ob_get_clean();
        } catch (\Exception $e) {
            ob_clean();
            throw $e;
        }

        error_reporting($errorReporting);

        return $result;
    }

    public function render($template = null, $variables = null)
    {
        if (is_array($template)) {
            $variables = $template;
            $template = null;
        }

        !$variables || $this->variables($variables);
        !$template || $this->template($template);

        $templateFile = $this->template();

        $content = $this->_render($templateFile);

        if ($this->layout) {
            $view = clone $this;
            $view->layout(false)
                ->template($this->layout())
                ->helpers['content'] = function() use ($content) {
                return $content;
            };

            return $view->render();
        }
        return $content;
    }

    public function display($template = null, $variables = null)
    {
        echo $this->render($template, $variables);
    }
}