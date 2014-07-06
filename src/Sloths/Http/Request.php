<?php

namespace Sloths\Http;

use Sloths\Http\Message\AbstractRequest;
use Sloths\Http\Message\Parameters;

class Request extends AbstractRequest
{
    /**
     * @var Parameters
     */
    protected $serverVars;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $path;

    /**
     * @param array|Parameters $vars
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setServerVars($vars)
    {
        if (is_array($vars)) {
            $vars = new Parameters($vars);
        }

        if (!$vars instanceof Parameters) {
            throw new \InvalidArgumentException(
                sprintf('Server vars must be an array or instance of %s\Parameters, %s given', __NAMESPACE__, gettype($vars))
            );
        }

        $this->serverVars = $vars;
        return $this;
    }

    /**
     * @return Parameters
     */
    public function getServerVars()
    {
        if (!$this->serverVars) {
            $this->serverVars = new Parameters($_SERVER?: []);
        }

        return $this->serverVars;
    }

    /**
     * @return Message\Headers
     */
    public function getHeaders()
    {
        if (!$this->headers) {
            $headers = [];

            foreach ($this->getServerVars() as $name => $value) {
                if ('HTTP_' == substr($name, 0, 5)) {
                    $headers[substr($name, 5)] = $value;
                }
            }

            parent::setHeaders($headers);
        }

        return $this->headers;
    }

    /**
     * @return string
     */
    public function getReferrer()
    {
        return $this->getHeaders()->get('REFERER');
    }

    /**
     * @return string
     */
    public function getPath()
    {
        if (!$this->path) {
            $serverVars = $this->getServerVars();
            $path = $serverVars->get('PATH_INFO')?: parse_url($serverVars->get('REQUEST_URI'), PHP_URL_PATH);
            $path = '/' . trim($path, ' /');
            $this->path = $path;
        }

        return $this->path;
    }

    /**
     * @return mixed
     */
    public function getOriginalMethod()
    {
        return $this->getServerVars()->get('REQUEST_METHOD');
    }

    /**
     * @return mixed|string
     */
    public function getMethod()
    {
        if (null === $this->method) {
            $originalMethod = $this->getOriginalMethod();

            if ($originalMethod == self::METHOD_POST) {
                $this->method = $this->getHeaders()->get('X_HTTP_METHOD_OVERRIDE')?: $this->getPostParams()->get('_method');
            }

            $this->method = $this->method?: $originalMethod;
        }

        return $this->method;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        if (!$this->url) {
            $this->url = $this->getScheme() . '://' . $this->getHost();
            $port = $this->getPort();

            if ($port != 80 && $port != 443) {
                $this->url .= ':' . $port;
            }

            $this->url .= $this->getServerVars()->get('REQUEST_URI');
        }

        return parent::getUrl();
    }

    /**
     * @return Parameters
     */
    public function getQueryParams()
    {
        if (!$this->queryParams) {
            $this->queryParams = new Parameters($_GET?: []);
        }

        return $this->queryParams;
    }

    /**
     * @return Parameters
     */
    public function getParams()
    {
        if (!$this->params) {
            $this->params = new Parameters(array_merge($this->getQueryParams()->toArray(), $this->getQueryParams()->toArray()));
        }

        return $this->params;
    }

    /**
     * @return Parameters
     */
    public function getFileParams()
    {
        if (!$this->files) {
            $this->files = new Parameters($_FILES? $this->mapFiles($_FILES) : []);
        }

        return $this->files;
    }

    /**
     * @param array $files
     * @return array
     */
    protected function mapFiles(array $files)
    {
        $result = array();
        foreach ($files as $name => $data) {
            foreach ($data as $param => $v) {
                $this->restructureFiles($result,
                    $name,
                    $files[$name][$param],
                    $param);
            }
        }

        return $result;
    }

    /**
     * @param $result
     * @param $name
     * @param $value
     * @param $param
     */
    function restructureFiles(&$result, $name, $value, $param)
    {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $this->restructureFiles($result[$name],
                    $k,
                    $v,
                    $param);
            }
        } else {
            $result[$name][$param] = $value;
        }
    }

    /**
     * @return Parameters
     */
    public function getCookieParams()
    {
        if (!$this->cookies) {
            $this->cookies = new Parameters($_COOKIE?: []);
        }

        return $this->cookies;
    }

    /**
     * @return string
     */
    public function getServerName()
    {
        return $this->getServerVars()->get('SERVER_NAME');
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return (int) $this->getServerVars()->get('SERVER_PORT');
    }

    /**
     * @param bool $withPort
     * @return string
     */
    public function getHost($withPort = false)
    {
        if (!$this->host) {
            $host = $this->getHeaders()->get('HOST');

            if ($host) {
                $parts = explode(':', $host);
                $host = $parts[0];
            } else {
                $host = $this->getServerName();
            }

            $this->host = $host;
        }

        if ($withPort) {
            return $this->host . ':' . $this->getPort();
        }

        return $this->host;
    }

    /**
     * @return string
     */
    public function getClientIp()
    {
        return $this->getServerVars()->get('REMOTE_ADDR');
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->getServerVars()->get('USER_AGENT');
    }

    /**
     * @return array
     */
    public function getAccepts()
    {
        $types = $this->getHeaders()->get('ACCEPT');
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
        return $this->getServerVars()->get('HTTPS') == 'on';
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
        return $this->getHeaders()->get('CONTENT_TYPE');
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
    public function isTrace()
    {
        return 'TRACE' == $this->getMethod();
    }

    /**
     * @return bool
     */
    public function isConnect()
    {
        return 'CONNECT' == $this->getMethod();
    }

    /**
     * @return bool
     */
    public function isXhr()
    {
        return 'XMLHttpRequest' == $this->getHeaders()->get('X_REQUESTED_WITH');
    }
}