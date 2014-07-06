<?php

namespace Sloths\Http\Message;

use Sloths\Util\UrlUtils;

/**
 * @property Parameters queryParams
 * @property Parameters postParams
 * @property Parameters params
 * @property Parameters cookies
 * @property Parameters files
 *
 */
abstract class AbstractRequest extends AbstractMessage implements RequestInterface
{
    const METHOD_HEAD          = 'HEAD';
    const METHOD_GET           = 'GET';
    const METHOD_POST          = 'POST';
    const METHOD_PUT           = 'PUT';
    const METHOD_PATCH         = 'PATCH';
    const METHOD_DELETE        = 'DELETE';
    const METHOD_OPTIONS       = 'OPTIONS';
    const METHOD_TRACE         = 'TRACE';
    const METHOD_CONNECT       = 'CONNECT';

    /**
     * @var array
     */
    protected $supportedMethods = [
        'HEAD', 'GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'TRACE', 'CONNECT', 
    ];

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var Parameters
     */
    protected $queryParams;

    /**
     * @var Parameters
     */
    protected $postParams;

    /**
     * @var Parameters
     */
    protected $params;

    /**
     * @var Parameters
     */
    protected $files;

    /**
     * @var Parameters
     */
    protected $cookies;

    /**
     * @param string $method
     * @throws \InvalidArgumentException If un supported method
     * @return $this
     */
    public function setMethod($method)
    {
        if (!in_array($method, $this->supportedMethods)) {
            throw new \InvalidArgumentException(sprintf('Un supported method %s', $method));
        }

        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $url
     * @throws \InvalidArgumentException If the URL is invalid
     * @return $this
     */
    public function setUrl($url)
    {
        if (false === filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException(sprintf('Invalid url %s', $url));
        }

        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $url = $this->url;

        if ($queryParams = $this->getQueryParams()->toArray()) {
            $url = UrlUtils::appendParams($url, $queryParams);
        }

        return $url;
    }

    /**
     * @param array|Parameters $params
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setQueryParams($params)
    {
        if (is_array($params)) {
            $params = new Parameters($params);
        }

        if (!$params instanceof Parameters) {
            throw new \InvalidArgumentException(
                sprintf('Query params must be an array or instance of %s\Parameters, %s given', __NAMESPACE__, gettype($params))
            );
        }

        $this->queryParams = $params;
        return $this;
    }

    /**
     * @return Parameters
     */
    public function getQueryParams()
    {
        if (!$this->queryParams) {
            $this->queryParams = new Parameters();
        }

        return $this->queryParams;
    }

    /**
     * @param array|Parameters $params
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setPostParams($params)
    {
        if (is_array($params)) {
            $params = new Parameters($params);
        }

        if (!$params instanceof Parameters) {
            throw new \InvalidArgumentException(
                sprintf('Post params must be an array or instance of %s\Parameters, %s given', __NAMESPACE__, gettype($params))
            );
        }

        $this->postParams = $params;
        return $this;
    }

    /**
     * @return Parameters
     */
    public function getPostParams()
    {
        if (!$this->postParams) {
            $this->postParams = new Parameters();
        }

        return $this->postParams;
    }

    /**
     * @param array|Parameters $params
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setParams($params)
    {
        if (is_array($params)) {
            $params = new Parameters($params);
        }

        if (!$params instanceof Parameters) {
            throw new \InvalidArgumentException(
                sprintf('Params must be an array or instance of %s\Parameters, %s given', __NAMESPACE__, gettype($params))
            );
        }

        $this->params = $params;
        return $this;
    }

    /**
     * @return Parameters
     */
    public function getParams()
    {
        if (!$this->params) {
            $this->params = new Parameters();
        }

        return $this->params;
    }

    /**
     * @param array|Parameters $files
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setFileParams($files)
    {
        if (is_array($files)) {
            $files = new Parameters($files);
        }

        if (!$files instanceof Parameters) {
            throw new \InvalidArgumentException(
                sprintf('File params must be an array or instance of %s\Parameters, %s given', __NAMESPACE__, gettype($files))
            );
        }

        $this->files = $files;
        return $this;
    }

    /**
     * @return Parameters
     */
    public function getFileParams()
    {
        if (!$this->files) {
            $this->files = new Parameters();
        }

        return $this->files;
    }

    /**
     * @param array|Parameters $cookies
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setCookieParams($cookies)
    {
        if (is_array($cookies)) {
            $cookies = new Parameters($cookies);
        }

        if (!$cookies instanceof Parameters) {
            throw new \InvalidArgumentException(
                sprintf('Cookie params must be an array or instance of %s\Parameters, %s given', __NAMESPACE__, gettype($cookies))
            );
        }

        $this->cookies = $cookies;
        return $this;
    }

    /**
     * @return Parameters
     */
    public function getCookieParams()
    {
        if (!$this->cookies) {
            $this->cookies = new Parameters();
        }

        return $this->cookies;
    }

    /**
     * @param string $name
     * @param array|Parameters $value
     * @return $this
     * @throws \BadMethodCallException
     */
    public function __set($name, $value)
    {
        switch ($name) {
            case 'queryParams': return $this->setQueryParams($value);
            case 'postParams': return $this->setPostParams($value);
            case 'params': return $this->setParams($value);
            case 'files': return $this->setFileParams($value);
            case 'cookies': return $this->setCookieParams($value);
        }

        return parent::__set($name, $value);
    }

    /**
     * @param string $name
     * @return Parameters
     * @throws \BadMethodCallException
     */
    public function __get($name)
    {
        switch ($name) {
            case 'queryParams': return $this->getQueryParams();
            case 'postParams': return $this->getPostParams();
            case 'params': return $this->getParams();
            case 'files': return $this->getFileParams();
            case 'cookies': return $this->getCookieParams();
        }

        return parent::__get($name);
    }
}