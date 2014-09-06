<?php

namespace Sloths\Http;

use Sloths\Misc\Parameters;
use Sloths\Misc\UrlUtils;

trait RequestTrait
{
    use MessageTrait;

    /**
     * @var array
     */
    protected static $supportedMethods = [
        self::METHOD_HEAD,
        self::METHOD_GET,
        self::METHOD_POST,
        self::METHOD_PUT,
        self::METHOD_PATCH,
        self::METHOD_DELETE,
        self::METHOD_OPTIONS,
        self::METHOD_TRACE,
        self::METHOD_CONNECT
    ];

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $scheme;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $port;

    /**
     * @var Parameters
     */
    protected $params;

    /**
     * @var \Sloths\Misc\Parameters
     */
    protected $paramsQuery;

    /**
     * @var \Sloths\Misc\Parameters
     */
    protected $paramsPost;

    /**
     * @var \Sloths\Misc\Parameters
     */
    protected $paramsFile;

    /**
     * @return array
     */
    public static function getSupportedMethods()
    {
        return self::$supportedMethods;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return mixed|string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        $path = trim($path);
        $path = trim($path);
        $path = '/' . trim($path, ' /');
        $this->path = $path;
        ;
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
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return UrlUtils::appendParams($this->url, $this->getParamsQuery()->toArray());
    }

    /**
     * @param string $scheme
     * @return $this
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
        return $this;
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @param string $host
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param int $port
     * @return $this
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param \Sloths\Misc\Parameters|array $params
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setParams($params)
    {
        if (is_array($params)) {
            $params = new Parameters($params);
        } else if (!$params instanceof Parameters) {
            throw new \InvalidArgumentException(sprintf(
                'Params must be an instance of Sloths\Misc\Parameters or an array. %s given', gettype($params)));
        }

        $this->params = $params;
        return $this;
    }

    /**
     * @return \Sloths\Misc\Parameters
     */
    public function getParams()
    {
        if (!$this->params) {
            $params = array_merge($this->getParamsQuery()->toArray(), $this->getParamsPost()->toArray());
            $this->params = new Parameters($params);
        }

        return $this->params;
    }

    /**
     * @param \Sloths\Misc\Parameters|array $params
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setParamsQuery($params)
    {
        if (is_array($params)) {
            $params = new Parameters($params);
        } else if (!$params instanceof Parameters) {
            throw new \InvalidArgumentException(sprintf(
                'Params must be an instance of Sloths\Misc\Parameters or an array. %s given', gettype($params)));
        }

        $this->paramsQuery = $params;
        return $this;
    }

    /**
     * @return \Sloths\Misc\Parameters
     */
    public function getParamsQuery()
    {
        if (!$this->paramsQuery) {
            $this->paramsQuery = new Parameters();
        }

        return $this->paramsQuery;
    }

    /**
     * @param \Sloths\Misc\Parameters|array $params
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setParamsPost($params)
    {
        if (is_array($params)) {
            $params = new Parameters($params);
        } else if (!$params instanceof Parameters) {
            throw new \InvalidArgumentException(sprintf(
                'Params must be an instance of Sloths\Misc\Parameters or an array. %s given', gettype($params)));
        }

        $this->paramsPost = $params;
        return $this;
    }

    /**
     * @return \Sloths\Misc\Parameters
     */
    public function getParamsPost()
    {
        if (!$this->paramsPost) {
            $this->paramsPost = new Parameters([]);
        }

        return $this->paramsPost;
    }

    /**
     * @param \Sloths\Misc\Parameters|array $params
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setParamsFile($params)
    {
        if (is_array($params)) {
            $params = new Parameters($params);
        } else if (!$params instanceof Parameters) {
            throw new \InvalidArgumentException(sprintf(
                'Params must be an instance of Sloths\Misc\Parameters or an array. %s given', gettype($params)));
        }

        $this->paramsFile = $params;
        return $this;
    }

    /**
     * @return \Sloths\Misc\Parameters
     */
    public function getParamsFile()
    {
        if (!$this->paramsFile) {
            $this->paramsFile = new Parameters();
        }

        return $this->paramsFile;
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
     * @return string
     */
    public function getContentType()
    {
        return $this->getHeaders()->get('CONTENT_TYPE');
    }
}