<?php

namespace Lazy\View;

use Lazy\Util\Inflector;
use Lazy\View\Exception as ViewException;

class View
{
    /**
     * @var array
     */
    protected $variables = [];

    /**
     * @var string
     */
    protected $path = '';

    /**
     * @var
     */
    protected $file;

    /**
     * @var string
     */
    protected $extension = 'html.php';

    /**
     * @var bool|string
     */
    protected $layout = false;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var array
     */
    protected $captures = [];

    /**
     * @var array
     */
    protected static $helperNamespaces = [
        'Lazy\View\Helper' => 'Lazy\View\Helper'
    ];

    /**
     * @param string $namespaceName
     */
    public static function addHelperNamespace($namespaceName)
    {
        static::$helperNamespaces[$namespaceName] = $namespaceName;
    }

    public function __call($method, $arguments)
    {
        foreach (self::$helperNamespaces as $namespace) {
            $helperClassName = $namespace . '\\' . Inflector::classify($method);
            if (class_exists($helperClassName) && is_subclass_of($helperClassName, 'Lazy\View\Helper\AbstractHelper')) {
                $helperClass = new $helperClassName($this);

                if (method_exists($helperClass, $method)) {
                    return call_user_func_array([$helperClass, $method], $arguments);
                }

                if (method_exists($helperClass, '__invoke')) {
                    return call_user_func_array($helperClass, $arguments);
                }

                throw new \RuntimeException(sprintf('%s: Invalid helper. Method %s or __invoke is required', $method, $method));

            }
        }

        throw new \BadMethodCallException(sprintf('Call undefined method %s', $method));
    }

    public function config(array $config)
    {
        !isset($config['path']) || $this->setPath($config['path']);
        !isset($config['layout']) || $this->setLayout($config['layout']);
        !isset($config['extension']) || $this->setExtension($config['extension']);

        return $this;
    }

    /**
     * @param array $variable
     * @return $this
     */
    public function setVars(array $variable)
    {
        $this->variables = $variable;
        return $this;
    }

    /**
     * @return array
     */
    public function getVars()
    {
        return $this->variables;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setVar($name, $value)
    {
        $this->variables[$name] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasVar($name)
    {
        return array_key_exists($name, $this->variables);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getVar($name)
    {
        return $this->hasVar($name)? $this->variables[$name] : null;
    }

    /**
     * @param array $variables
     * @return $this
     */
    public function addVars(array $variables)
    {
        $this->variables = array_merge($this->variables, $variables);
        return $this;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

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
     * @param string $file
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param bool|string $layout
     * @return $this
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * @return bool|string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        if (!$this->file) {
            return;
        }

        $file = $this->file;

        if (DIRECTORY_SEPARATOR != $file[0]) {
            $file = $this->path . '/' . $file;
        }

        if (!pathinfo($file, PATHINFO_EXTENSION)) {
            $file .= '.' . $this->extension;
        }

        return $file;
    }

    public function getLayoutFilePath()
    {
        if (!$this->layout) {
            return;
        }

        $file = $this->layout;

        if (DIRECTORY_SEPARATOR != $file[0]) {
            $file = $this->path . '/_layouts/' . $file;
        }

        if (!pathinfo($file, PATHINFO_EXTENSION)) {
            $file .= '.' . $this->extension;
        }

        return $file;
    }

    /**
     * @return string
     */
    public function content()
    {
        return $this->content;
    }

    /**
     * @param string $__file__
     * @return string
     */
    protected function _render($__file__)
    {
        extract($this->variables);
        ob_start();
        require $__file__;
        return ob_get_clean();
    }

    /**
     * @param string [$file=null]
     * @param array [$variables=null]
     * @return string
     * @throws ViewException
     */
    public function render($file = null, array $variables = null)
    {
        if (is_array($file)) {
            $variables = $file;
            $file = null;
        }

        !$file || $this->setFile($file);
        !$variables || $this->setVars($variables);

        if (!($file = $this->getFilePath())) {
            throw new \InvalidArgumentException('A view file is required');
        }

        if (!file_exists($file)) {
            throw new \InvalidArgumentException(sprintf('%s: No such view file', $file));
        }

        $content = $this->_render($file);

        if ($this->layout) {
            $viewLayout = clone $this;
            $viewLayout->setLayout(false);
            $viewLayout->content = $content;
            return $viewLayout->render($this->getLayoutFilePath());
        }

        return $content;
    }

    /**
     * @param $str
     * @return string
     */
    public function escape($str)
    {
        return htmlspecialchars($str);
    }

    /**
     * @param string $key
     * @param string [$value=null]
     * @return Capture
     */
    public function capture($key, $value = null)
    {
        if (!isset($this->captures[$key])) {
            $this->captures[$key] = new Capture();
        }

        $capture = $this->captures[$key];

        if ($value) {
            $capture->append($value);
        }

        return $capture;
    }

    public function __toString()
    {
        return $this->render();
    }
}