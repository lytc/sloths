<?php

namespace Lazy\Http;

use Lazy\Http\Exception\Exception;
use Lazy\Environment\Environment;

class Request
{
    protected static $aliasMethods = [
        'serverVars'    => 'env',
        'headers'       => 'env',
        'header'        => 'env',
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
        'pickParams'    => 'env',
        'body'          => 'env'
    ];
    protected $env;
    protected $pathInfoOverrides = [];
    protected $basePath = '';

    public static $methodOverrideParamName = '_method';

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

    public function basePath($basePath = null)
    {
        if (!func_num_args()) {
            return $this->basePath;
        }

        $this->basePath = $basePath;
        return $this;
    }

    public function pathInfoOverrides(array $value)
    {
        $this->pathInfoOverrides = $value;
        return $this;
    }

    public function getFullPathInfo()
    {
        $pathInfo = $this->serverVar('PATH_INFO')?: $this->serverVar('REQUEST_URI');
        $pathInfo || $pathInfo = '/';
        $pathInfo = parse_url($pathInfo, PHP_URL_PATH);

        if (isset($this->pathInfoOverrides[$pathInfo])) {
            $pathInfo = $this->pathInfoOverrides[$pathInfo];
        }

        if ('/' !== $pathInfo) {
            $pathInfo = preg_replace('/\/+$/', '', $pathInfo);
        }

        return $pathInfo;
    }

    public function pathInfo($pathInfo = null)
    {
        if (!func_num_args()) {
            $pathInfo = $this->getFullPathInfo();
            $basePath = $this->basePath;

            if ($basePath) {
                $pathInfo = preg_replace('/^' . preg_quote($basePath, '/') . '/', '', $pathInfo);
            }

            return $pathInfo?: '/';
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
            $methodOverride = $this->paramPost(self::$methodOverrideParamName)?: $this->header('X_HTTP_METHOD_OVERRIDE');

            if ('POST' == $method && in_array($methodOverride, ['PUT', 'DELETE'])) {
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

    public function contentType($contentType = null)
    {
        if (!func_num_args()) {
            return $this->header('CONTENT_TYPE');
        }

        $this->header('CONTENT_TYPE', $contentType);
        return $this;
    }

    public function baseUrl()
    {
        $scheme = $this->scheme();
        $host   = $this->host();
        $port   = $this->serverPort();

        $result = $scheme . '://' . $host;

        $defaultPorts = ['http' => 80, 'https' => 443];

        if ($port != $defaultPorts[$scheme]) {
            $result .= ':' . $port;
        }

        return $result;
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