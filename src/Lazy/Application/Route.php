<?php

namespace Lazy\Application;

class Route
{
    protected $methods = [];
    protected $pattern;
    protected $conditions = [];

    public function __construct($methods = null, $pattern = null, array $conditions = [])
    {
        !$methods || $this->methods($methods);
        !$pattern || $this->pattern($pattern);
        !$conditions || $this->conditions($conditions);
    }

    /**
     * @param bool $reset
     * @param null $methods
     * @return $this|array
     */
    public function methods($reset = false, $methods = null)
    {
        if (!func_num_args()) {
            return $this->methods;
        }

        if (!is_bool($reset)) {
            $methods = $reset;
            $reset = false;
        }

        if (!is_array($methods)) {
            $methods = (String) $methods;
            $methods = preg_split('/\s+/', trim($methods));
        }

        if ($reset) {
            $this->methods = $methods;
        } else {
            $this->methods = array_merge($this->methods, $methods);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function method()
    {
        return call_user_func_array([$this, 'methods'], func_get_args());
    }

    public function hasMethods($methods)
    {
        if (!is_array($methods)) {
            $methods = (String) $methods;
            $methods = preg_split('/\s+/', trim($methods));
        }
        $intersect = array_intersect($this->methods, $methods);
        return !empty($intersect);
    }

    public function hasMethod()
    {
        return call_user_func_array([$this, 'hasMethods'], func_get_args());
    }

    public function pattern($pattern = null)
    {
        if (!func_num_args()) {
            return $this->pattern;
        }

        $this->pattern = $pattern;
        return $this;
    }

    protected function _compilePattern($pattern)
    {
        if ('#' == $pattern[0]) {
            return ['#^' . substr($pattern, 1) . '$#', []];
        }
        $keys = [];

        $pattern = preg_replace_callback('/[^\?\%\\/\:\*\w]/', function($matches) {
            return $this->_encoded($matches[0]);
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

    protected function _encoded($char)
    {
        return '(?:' . preg_quote($char, '#') . '|' . preg_quote(urlencode($char), '#') . '|%' . bin2hex($char) . ')';
    }

    public function conditions($reset = false, $conditions = null)
    {
        if (!func_num_args()) {
            return $this->conditions;
        }

        if (!is_bool($reset)) {
            $conditions = $reset;
            $reset = false;
        }

        if (!is_array($conditions)) {
            $conditions = [$conditions];
        }

        if ($reset) {
            $this->conditions = $conditions;
        } else {
            $this->conditions = array_merge($this->conditions, $conditions);
        }

        return $this;
    }

    public function matches($method, $uri)
    {
        if (!in_array($method, $this->methods)) {
            return false;
        }

        list($pattern, $keys) = $this->_compilePattern($this->pattern);

        if (!preg_match($pattern, $uri, $matches)) {
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
                $params[$key] = urldecode($matches[$index]);
            }

        }

        foreach ($matches as $key => $value) {
            if (!is_numeric($key)) {
                $params[$key] = urldecode($value);
            }
        }

        foreach ($this->conditions as $key => $condition) {
            if ($condition instanceof \Closure) {
                if (!$condition($params)) {
                    return false;
                }
            } else {
                if (!isset($params[$key])) {
                    return false;
                }

                if (!preg_match('#' . $condition . '#', $params[$key])) {
                    return false;
                }
            }
        }

        $params || $params = $matches;

        return $params;
    }
}