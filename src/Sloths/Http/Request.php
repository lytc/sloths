<?php

namespace Sloths\Http;

use Sloths\Misc\ArrayContainer;

class Request extends AbstractMessage
{
    /**
     * @var \Closure
     */
    protected static $defaultGetMethodCallback;

    /**
     * @var array
     */
    protected $superGlobals;
    /**
     * @var string
     */
    protected $path;

    /**
     * @var \Closure
     */
    protected $getMethodCallback;

    /**
     * @var bool
     */
    protected $processedHeaders = false;

    /**
     * @var ArrayContainer
     */
    protected $_params;

    /**
     * @var ArrayContainer
     */
    protected $paramsGet;

    /**
     * @var ArrayContainer
     */
    protected $paramsPost;

    /**
     * @var ArrayContainer
     */
    protected $paramsCookie;

    /**
     * @var ArrayContainer
     */
    protected $paramsHeaders;

    /**
     * @var bool
     */
    protected static $rawPostBody = false;

    /**
     * @param callable $callback
     */
    public static function setDefaultGetMethodCallback(\Closure $callback)
    {
        static::$defaultGetMethodCallback = $callback;
    }

    /**
     * @return callable
     */
    public static function getDefaultGetMethodCallback()
    {
        return static::$defaultGetMethodCallback?: function($request) {
            $method = $originalMethod = $request->getOriginalMethod();
            if ('POST' == $originalMethod) {
                $method = $request->getHeader('X_HTTP_METHOD_OVERRIDE')?: ($request->getVar('_method')?: $originalMethod);
            }

            return $method;
        };
    }

    /**
     * @param array $superGlobals
     */
    public function __construct(array $superGlobals = null)
    {
        if (!$superGlobals) {
            $superGlobals = [
                '_SERVER'   => $_SERVER,
                '_REQUEST'  => $_REQUEST,
                '_GET'      => $_GET,
                '_POST'     => $_POST,
                '_COOKIE'   => $_COOKIE,
                '_FILES'    => $_FILES,
            ];
        }

        foreach (['_GET', '_POST', '_COOKIE', '_FILES', '_SERVER'] as $section) {
            isset($superGlobals[$section]) || $superGlobals[$section] = [];
        }

        if (!isset($superGlobals['_REQUEST']) || !$superGlobals['_REQUEST']) {
            $superGlobals['_REQUEST'] = array_replace($superGlobals['_GET']?: [], $superGlobals['_POST']?: [], $superGlobals['_COOKIE']?: []);
        }

        $this->superGlobals = $superGlobals;
    }

    public function __get($name)
    {
        switch ($name) {
            case 'params': return $this->params();
            case 'get': return $this->paramsGet();
            case 'post': return $this->paramsPost();
            case 'cookie': return $this->paramsCookie();
            case 'headers': return $this->headers();
        }

        throw new \InvalidArgumentException(sprintf('Call to undefined property %s', $name));
    }

    /**
     * @return ArrayContainer
     */
    public function params()
    {
        if (!$this->_params) {
            $this->_params = new ArrayContainer($this->getVars());
        }

        return $this->_params;
    }

    /**
     * @return ArrayContainer
     */
    public function paramsGet()
    {
        if (!$this->paramsGet) {
            $this->paramsGet = new ArrayContainer($this->getGetVars());
        }

        return $this->paramsGet;
    }

    /**
     * @return ArrayContainer
     */
    public function paramsPost()
    {
        if (!$this->paramsPost) {
            $this->paramsPost = new ArrayContainer($this->getPostVars());
        }

        return $this->paramsPost;
    }

    /**
     * @return ArrayContainer
     */
    public function paramsCookie()
    {
        if (!$this->paramsCookie) {
            $this->paramsCookie = new ArrayContainer($this->getCookieVars());
        }

        return $this->paramsCookie;
    }

    /**
     * @return ArrayContainer
     */
    public function headers()
    {
        if (!$this->paramsHeaders) {
            $this->paramsHeaders = new ArrayContainer($this->getHeaders());
        }

        return $this->paramsHeaders;
    }

    /**
     * @param $section
     * @param $name
     * @param null $default
     * @return null
     */
    protected function getSuperGlobalVar($section, $name, $default = null)
    {
        return isset($this->superGlobals[$section][$name])? $this->superGlobals[$section][$name] : $default;
    }

    /**
     * @param string $name
     * @param mixed [$default=null]
     * @return mixed|string
     */
    public function getServerVar($name, $default = null)
    {
        return $this->getSuperGlobalVar('_SERVER', $name, $default);
    }

    /**
     *
     */
    protected function processedHeaders()
    {
        if (!$this->processedHeaders) {
            foreach ($this->superGlobals['_SERVER'] as $name => $value) {
                if ('HTTP_' == substr($name, 0, 5)) {
                    $this->setHeader(substr($name, 5), $value);
                }
            }

            $this->processedHeaders = true;
        }
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        $this->processedHeaders();
        return parent::getHeaders();
    }

    /**
     * @return string
     */
    public function getHeaderAsString()
    {
        $this->processedHeaders();
        return parent::getHeaderAsString();
    }

    /**
     * @param string $name
     * @return mixed|string
     */
    public function getHeader($name)
    {
        $this->processedHeaders();
        return parent::getHeader($name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasHeader($name)
    {
        $this->processedHeaders();
        return parent::hasHeader($name);
    }

    /**
     * @return null|string
     */
    public function getBody()
    {
        if (null === $this->body) {
            if ($this->getOriginalMethod() == 'POST' && false == static::$rawPostBody) {
                static::$rawPostBody = file_get_contents('php://input');
            }

            $this->body = static::$rawPostBody;
        }

        return $this->body;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        if (null === $this->path) {
            $path = $this->getServerVar('PATH_INFO')?: parse_url($this->getUrl(), PHP_URL_PATH);
            $this->path = rtrim($path, '/')?: '/';
        }

        return $this->path;
    }

    public function getUrl()
    {
        return $this->getServerVar('REQUEST_URI');
    }

    /**
     * @return mixed|string
     */
    public function getOriginalMethod()
    {
        return $this->getServerVar('REQUEST_METHOD');
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function setGetMethodCallback(\Closure $callback)
    {
        $this->getMethodCallback = $callback;
        return $this;
    }

    /**
     * @return \Closure
     */
    public function getGetMethodCallback()
    {
        return $this->getMethodCallback?: static::getDefaultGetMethodCallback();
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        $callback = $this->getGetMethodCallback();
        return call_user_func($callback, $this);
    }

    /**
     * @return string
     */
    public function getReferrer()
    {
        return $this->getHeader('REFERER');
    }

    /**
     * @return string
     */
    public function getServerName()
    {
        return $this->getServerVar('SERVER_NAME');
    }

    /**
     * @return int
     */
    public function getServerPort()
    {
        return $this->getServerVar('SERVER_PORT');
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->getHeader('HOST');
    }

    /**
     * @return string
     */
    public function getServerIp()
    {
        return $this->getServerVar('SERVER_ADDR');
    }

    /**
     * @return string
     */
    public function getClientIp()
    {
        return $this->getServerVar('REMOTE_ADDR');
    }

    /**
     * @return int
     */
    public function getClientPort()
    {
        return $this->getServerVar('REMOTE_PORT');
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->getHeader('USER_AGENT');
    }

    /**
     * @return array
     */
    public function getAccepts()
    {
        $types = $this->getHeader('ACCEPT');
        $types = explode(',', $types);
        return $types;
    }

    /**
     * @param string $type
     * @return bool
     */
    public function isAccept($type)
    {
        return in_array($type, $this->getAccepts());
    }

    /**
     * @return bool
     */
    public function isSecure()
    {
        return $this->getServerVar('HTTPS') == 'on';
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->isSecure()? 'https' : 'http';
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->getHeader('CONTENT_TYPE');
    }

    /**
     * @param string $section
     * @param string $name
     * @return bool
     */
    public function _hasVar($section, $name)
    {
        return array_key_exists($name, $this->superGlobals[$section]);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasVar($name)
    {
        $vars = array_replace($this->getGetVars(), $this->getPostVars(), $this->getCookieVars(), $this->getFileVars());
        return array_key_exists($name, $vars);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasGetVar($name)
    {
        return $this->_hasVar('_GET', $name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasPostVar($name)
    {
        return $this->_hasVar('_POST', $name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasCookieVar($name)
    {
        return $this->_hasVar('_COOKIE', $name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasFileVar($name)
    {
        return $this->_hasVar('_FILES', $name);
    }

    /**
     * @param $name
     * @param mixed [$default=null]
     * @return mixed
     */
    public function getVar($name, $default = null)
    {
        return $this->getSuperGlobalVar('_REQUEST', $name, $default);
    }

    /**
     * @return array
     */
    public function getVars()
    {
        return array_merge($this->getGetVars(), $this->getPostVars(), $this->getCookieVars(), $this->getFileVars());
    }

    /**
     * @param string $name
     * @param mixed [$default=null]
     * @return mixed
     */
    public function getGetVar($name, $default = null)
    {
        return $this->getSuperGlobalVar('_GET', $name, $default);
    }

    /**
     * @return array
     */
    public function getGetVars()
    {
        return $this->superGlobals['_GET'];
    }

    /**
     * @param string $name
     * @param mixed [$default=null]
     * @return mixed
     */
    public function getPostVar($name, $default = null)
    {
        return $this->getSuperGlobalVar('_POST', $name, $default);
    }

    /**
     * @return array
     */
    public function getPostVars()
    {
        return $this->superGlobals['_POST'];
    }

    /**
     * @param string $name
     * @param mixed [$default=null]
     * @return mixed
     */
    public function getCookieVar($name, $default = null)
    {
        return $this->getSuperGlobalVar('_COOKIE', $name, $default);
    }

    /**
     * @return array
     */
    public function getCookieVars()
    {
        return $this->superGlobals['_COOKIE'];
    }

    /**
     * @param string $name
     * @param mixed [$default=null]
     * @return mixed
     */
    public function getFileVar($name, $default = null)
    {
        return $this->getSuperGlobalVar('_FILES', $name, $default);
    }

    /**
     * @return array
     */
    public function getFileVars()
    {
        return $this->superGlobals['_FILES'];
    }

    /**
     * @return bool
     */
    public function isHead()
    {
        return 'HEAD' == $this->getMethod();
    }

    /**
     * @return bool
     */
    public function isGet()
    {
        return 'GET' == $this->getMethod();
    }

    /**
     * @return bool
     */
    public function isPost()
    {
        return 'POST' == $this->getMethod();
    }

    /**
     * @return bool
     */
    public function isPut()
    {
        return 'PUT' == $this->getMethod();
    }

    /**
     * @return bool
     */
    public function isPatch()
    {
        return 'PATCH' == $this->getMethod();
    }

    /**
     * @return bool
     */
    public function isDelete()
    {
        return 'DELETE' == $this->getMethod();
    }

    /**
     * @return bool
     */
    public function isOptions()
    {
        return 'OPTIONS' == $this->getMethod();
    }

    /**
     * @return bool
     */
    public function isXhr()
    {
        return 'XMLHttpRequest' == $this->getHeader('X_REQUESTED_WITH');
    }
}