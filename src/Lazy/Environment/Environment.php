<?php

namespace Lazy\Environment;

use Lazy\Environment\Exception\Exception;

class Environment
{
    protected $data;
    protected $body;
    protected static $_body;

    public function __construct()
    {
        $this->data = [
            'serverVars'    => $_SERVER,
            'paramsGet'     => $_GET,
            'paramsPost'    => $_POST,
            'paramsFile'    => $_FILES,
            'paramsCookie'  => $_COOKIE,
            'params'        => [],
        ];

        $this->data['params'] = array_replace([], $_GET?: [], $_POST?: [], $_COOKIE?: []);

        if ('application/json' == $this->header('CONTENT_TYPE') || 'application/json' == $this->serverVar('CONTENT_TYPE')) {
            $body = $this->body()?: '{}';
            $this->params(json_decode($body, true));
        }
    }

    public function __call($method, $args)
    {
        if (!array_key_exists($method, $this->data)) {
            throw new Exception("Call undefined method $method");
        }

        $name = isset($args[0])? $args[0] : null;
        $value = isset($args[1])? $args[1] : null;
        $data = $this->data[$method];

        switch (count($args)) {
            case 0: return $data;
            case 1:
                if (is_array($name)) {
                    $this->data[$method] = array_replace($data, $name);
                    return $this;
                }
                if ('params' == $method) {
                    if (isset($data[$name])) {
                        return $data[$name];
                    }

                    foreach (['paramsGet', 'paramsPost', 'paramsCookie', 'params'] as $type) {
                        if (isset($this->data[$type][$name])) {
                            return $this->data[$type][$name];
                        }
                    }

                    return null;

                } else {
                    return isset($data[$name])? $data[$name] : null;
                }

            default:
                if (true === $name) {
                    $this->data[$method] = (array) $value;
                } else {
                    $this->data[$method][$name] = $value;
                }
        }
        return $this;
    }

    public function serverVar()
    {
        return call_user_func_array([$this, 'serverVars'], func_get_args());
    }

    public function headers($name = null, $value = null)
    {
        switch (func_num_args()) {
            case 0:
                $headers = [];
                foreach ($this->serverVars() as $name => $value) {
                    if ('HTTP_' == substr($name, 0, 5)) {
                        $headers[substr($name, 5)] = $value;
                    }
                }
                return $headers;

            case 1: return $this->serverVars('HTTP_' . $name);
            case 2: $this->serverVars('HTTP_' . $name, $value);
        }
        return $this;
    }

    public function header()
    {
        return call_user_func_array([$this, 'headers'], func_get_args());
    }

    public function body($body = null)
    {
        if (!func_num_args()) {
            if (null === self::$_body) {
                self::$_body = file_get_contents('php://input');
            }
            if (null === $this->body) {
                $this->body = self::$_body;
            }
            return $this->body;
        }

        $this->body = (String) $body;
    }

    public function paramGet()
    {
        return call_user_func_array([$this, 'paramsGet'], func_get_args());
    }

    public function paramPost()
    {
        return call_user_func_array([$this, 'paramsPost'], func_get_args());
    }

    public function paramCookie()
    {
        return call_user_func_array([$this, 'paramsCookie'], func_get_args());
    }

    public function paramFile()
    {
        return call_user_func_array([$this, 'paramsFile'], func_get_args());
    }

    public function param()
    {
        return call_user_func_array([$this, 'params'], func_get_args());
    }

    public function pickParams($names)
    {
        $names = trim($names);
        $names = preg_replace('/\s+/', ' ', $names);
        $names = explode(' ', $names);

        $result = [];
        $params = $this->params();
        foreach ($names as $name) {
            if (array_key_exists($name, $params)) {
                $result[$name] = $params[$name];
            }
        }

        return $result;
    }
}