<?php

namespace Lazy\Http;

use Lazy\Http\Exception\Exception;
use Lazy\Environment\Environment;

class Request
{
    protected static $aliasMethods = [
        'serverVars'    => 'env',
        'paramsGet'     => 'env',
        'paramsPost'    => 'env',
        'paramsCookie'  => 'env',
        'paramsFile'    => 'env',
        'params'        => 'env',
        'serverVar'     => 'env',
        'paramGet'      => 'env',
        'paramPost'     => 'env',
        'paramCookie'   => 'env',
        'paramFile'     => 'env',
        'param'         => 'env',
    ];
    protected $env;
    protected $pathInfoOverrides = [];

    public static $methodOverrideParamName = '_method';
    protected $body;

    protected $config = [];

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

    public function env(Environment $env = null)
    {
        if (!$env) {
            $this->env || $this->env = new Environment($this);
            return $this->env;
        }

        $this->env = $env;
        return $this;
    }

    public function __call($method, $args)
    {
        if (isset(self::$aliasMethods[$method])) {
            return call_user_func_array([$this->{self::$aliasMethods[$method]}(), $method], $args);
        }

        throw new Exception("Call undefined method $method");
    }

    public function body($body = null)
    {
        if (!func_num_args()) {
            if (null === $this->body) {
                $this->body = file_get_contents('php://input');
            }
            return $this->body;
        }

        $this->body = (String) $body;
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

    public function pathInfoOverrides(array $value)
    {
        $this->pathInfoOverrides = $value;
        return $this;
    }

    public function pathInfo($pathInfo = null)
    {
        if (!func_num_args()) {
            $pathInfo = $this->serverVar('PATH_INFO')?: $this->serverVar('REQUEST_URI');
            $pathInfo || $pathInfo = '/';
            $pathInfo = parse_url($pathInfo, PHP_URL_PATH);

            if (isset($this->pathInfoOverrides[$pathInfo])) {
                $pathInfo = $this->pathInfoOverrides[$pathInfo];
            }

            return $pathInfo;
        }

        $this->serverVars('PATH_INFO', $pathInfo);
        return $this;
    }

    public function referrer($referrer = null)
    {
        if (!func_num_args()) {
            return $this->header('REFERER');
        }

        $this->header('REFERER', $referrer);
        return $this;
    }

    public function serverName($serverName = null)
    {
        if (!func_num_args()) {
            return $this->serverVars('SERVER_NAME');
        }

        $this->serverVars('SERVER_NAME', $serverName);
        return $this;
    }

    public function serverPort($serverPort = null)
    {
        if (!func_num_args()) {
            return $this->serverVars('SERVER_PORT');
        }

        $this->serverVars('SERVER_PORT', $serverPort);
        return $this;
    }

    public function host($host = null)
    {
        if (!func_num_args()) {
            return $this->header('HOST');
        }

        $this->header('HOST', $host);
        return $this;
    }

    public function serverIp($serverIp = null)
    {
        if (!func_num_args()) {
            return $this->serverVars('SERVER_ADDR');
        }

        $this->serverVars('SERVER_ADDR', $serverIp);
        return $this;
    }

    public function clientIp($clientIp = null)
    {
        if (!func_num_args()) {
            return $this->serverVars('REMOTE_ADDR');
        }

        $this->serverVars('REMOTE_ADDR', $clientIp);
        return $this;
    }

    public function clientPort($clientPort = null)
    {
        if (!func_num_args()) {
            return $this->serverVars('REMOTE_PORT');
        }

        $this->serverVars('REMOTE_PORT', $clientPort);
    }

    public function userAgent($userAgent = null)
    {
        if (!func_num_args()) {
            return $this->header('USER_AGENT');
        }

        $this->header('USER_AGENT', $userAgent);
        return $this;
    }

    public function accepts($types = null)
    {
        if (!func_num_args()) {
            $types = $this->header('ACCEPT');
            $types = explode(',', $types);
            return $types;
        }

        if (is_array($types)) {
            $types = implode(',', $types);
        }

        $this->header('ACCEPT', $types);
        return $this;
    }

    public function isAccept($type)
    {
        return in_array($type, $this->accepts());
    }

    public function method($method = null)
    {
        if (!func_num_args()) {
            $method = $this->serverVars('REQUEST_METHOD');
            if ('POST' == $method && in_array($methodOverride = $this->paramPost(self::$methodOverrideParamName), ['PUT', 'DELETE'])) {
                return $methodOverride;
            }
            return $method;
        }

        $this->serverVars('REQUEST_METHOD', $method);
        return $this;
    }

    public function scheme()
    {
        return $this->isSecure()? 'https' : 'http';
    }

    public function isGet()
    {
        return $this->method() == 'GET';
    }

    public function isPost()
    {
        return $this->method() == 'POST';
    }

    public function isPut()
    {
        return $this->method() == 'PUT';
    }

    public function isDelete()
    {
        return $this->method() == 'DELETE';
    }

    public function isOptions()
    {
        return $this->method() == 'OPTIONS';
    }

    public function isTrace()
    {
        return $this->method() == 'TRACE';
    }

    public function isPatch()
    {
        return $this->method() == 'PATCH';
    }

    public function isConnect()
    {
        return $this->method() == 'CONNECT';
    }

    public function isPropFind()
    {
        return $this->method() == 'PROPFIND';
    }

    public function isXhr()
    {
        return $this->header('X_REQUESTED_WITH') == 'XMLHttpRequest';
    }

    public function isSecure()
    {
        return $this->serverVar('HTTPS') == 'on';
    }
}