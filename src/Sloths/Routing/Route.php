<?php

namespace Sloths\Routing;

class Route
{
    /**
     * @var array
     */
    protected $methods = [];

    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var
     */
    protected $params;

    /**
     * @param string $methods
     * @param string $pattern
     * @param array $conditions
     */
    public function __construct($methods = null, $pattern = null, callable $callback = null)
    {
        !$methods || $this->setMethod($methods);
        !$pattern || $this->setPattern($pattern);
        $this->callback = $callback;
    }

    /**
     * @param string|array $methods
     * @return $this
     */
    public function setMethod($methods)
    {
        if (!is_array($methods)) {
            $methods = (String) $methods;
            $methods = preg_split('/\s+/', trim($methods));
        }

        $this->methods = array_combine($methods, $methods);

        return $this;
    }

    /**
     * @return array
     */
    public function getMethod()
    {
        return $this->methods;
    }

    /**
     * @param string$pattern
     * @return $this
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param string $pattern
     * @return array
     */
    protected function compilePattern($pattern)
    {
        if ('#' == $pattern[0]) {
            return ['#^' . substr($pattern, 1) . '$#', []];
        }
        $keys = [];

        $pattern = preg_replace_callback('/[^\?\%\\/\:\*\w]/', function($matches) {
            return $this->encoded($matches[0]);
        }, $pattern);

        $pattern = preg_replace_callback('/((::?\w+)|\*)/', function($matches) use(&$keys) {
            if ($matches[1] == '*') {
                $keys[] = 'splat';
                return '(.*?)';
            }

            $keys[] = substr($matches[2], 1);
            if ('::' == substr($matches[2], 0, 2)) {
                return '(\d+)';
            } else {
                return '([^/?\#]+)';
            }

        }, $pattern);

        $pattern = '#^' . $pattern . '$#';
        return [$pattern, $keys];
    }

    /**
     * @param string $char
     * @return string
     */
    protected function encoded($char)
    {
        return '(?:' . preg_quote($char, '#') . '|' . preg_quote(urlencode($char), '#') . '|%' . bin2hex($char) . ')';
    }

    /**
     * @param $method
     * @param $path
     * @return array|bool
     */
    public function match($method, $path)
    {
        if ($this->methods && !in_array($method, $this->methods)) {
            return false;
        }

        list($pattern, $keys) = $this->compilePattern($this->pattern);

        if (!preg_match($pattern, $path, $matches)) {
            return false;
        }

        $params = [];
        array_shift($matches);

        foreach ($keys as $index => $key) {
            if ($key == 'splat') {
                isset($params['splat']) || $params['splat'] = [];
                $params['splat'][] = urldecode($matches[$index]);
                continue;
            }

            if (':' == $key[0]) {
                $params[substr($key, 1)] = (int) $matches[$index];
            } else {
                $params[$key] = isset($matches[$index])? urldecode($matches[$index]) : null;
            }

        }

        foreach ($matches as $key => $value) {
            if (!is_numeric($key)) {
                $params[$key] = urldecode($value);
            }
        }

        $params || $params = $matches;
        $this->params = $params;
        return $params;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}