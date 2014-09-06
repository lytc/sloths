<?php

namespace Sloths\Http;

use Sloths\Misc\Parameters;

interface RequestInterface extends MessageInterface {

    const METHOD_HEAD      = 'HEAD';
    const METHOD_GET       = 'GET';
    const METHOD_POST      = 'POST';
    const METHOD_PUT       = 'PUT';
    const METHOD_PATCH     = 'PATCH';
    const METHOD_DELETE    = 'DELETE';
    const METHOD_OPTIONS   = 'OPTIONS';
    const METHOD_TRACE     = 'TRACE';
    const METHOD_CONNECT   = 'DELETE';

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod($method);

    /**
     * @return string
     */
    public function getMethod();

    /**
     * @param string $path
     * @return $this
     */
    public function setPath($path);

    /**
     * @return string
     */
    public function getPath();

    /**
     * @param \Sloths\Misc\Parameters|array $params
     * @return $this
     */
    public function setParams($params);

    /**
     * @return \Sloths\Misc\Parameters
     */
    public function getParams();

    /**
     * @param \Sloths\Misc\Parameters|array $params
     * @return $this
     */
    public function setParamsQuery($params);

    /**
     * @return \Sloths\Misc\Parameters
     */
    public function getParamsQuery();

    /**
     * @param \Sloths\Misc\Parameters|array $params
     * @return $this
     */
    public function setParamsPost($params);

    /**
     * @return \Sloths\Misc\Parameters
     */
    public function getParamsPost();

    /**
     * @param \Sloths\Misc\Parameters|array $params
     * @return $this
     */
    public function setParamsFile($params);

    /**
     * @return \Sloths\Misc\Parameters
     */
    public function getParamsFile();

    /**
     * @param string $scheme
     * @return $this
     */
    public function setScheme($scheme);

    /**
     * @return string
     */
    public function getScheme();

    /**
     * @param string $host
     * @return $this
     */
    public function setHost($host);
    /**
     * @return string
     */
    public function getHost();

    /**
     * @param int $port
     * @return $this
     */
    public function setPort($port);
    /**
     * @return int
     */
    public function getPort();

//    /**
//     * @return string
//     */
//    public function getReferrer();

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url);

    /**
     * @return string
     */
    public function getUrl();
}