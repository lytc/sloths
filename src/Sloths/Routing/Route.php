<?php

namespace Sloths\Routing;

use Sloths\Http\AbstractRequest;

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
     * @param $methods
     * @param $pattern
     * @param callable $callback
     */
    public function __construct($methods, $pattern, callable $callback)
    {
        if ('*' == $methods) {
            $methods = AbstractRequest::getSupportedMethods();
        } else {
            if (!is_array($methods)) {
                $methods = [$methods];
            }
        }

        $this->methods = array_values($methods);

        $this->pattern = $pattern;
        $this->callback = $callback;
    }

    /**
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @param $method
     * @return bool
     */
    public function hasMethod($method)
    {
        return false !== array_search($method, $this->getMethods());
    }

    /**
     * @return mixed
     */
    public function getPattern()
    {
        return $this->pattern;
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
     * @param string $method
     * @param string $path
     * @return bool|MathResult
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

        return $params;
    }
}