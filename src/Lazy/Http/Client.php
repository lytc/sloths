<?php

namespace Lazy\Http;

use Lazy\Http\Client\Exception\Exception;
use Lazy\Http\Client\Response;

class Client
{
    protected static $defaultBaseUrl;
    protected static $defaultHeaders = [];
    protected static $defaultParamsGet = [];
    protected static $defaultParamsPost = [];
    protected static $defaultCurlOptions = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLINFO_HEADER_OUT => true
    ];

    protected $baseUrl;
    protected $headers = [];
    protected $paramsGet = [];
    protected $paramsPost = [];
    protected $rawPostData;

    protected $method = 'GET';
    protected $url;
    protected $curl;
    protected $curlOptions = [];

    protected $responseClass = 'Lazy\Http\Client\Response';

    public static function defaultBaseUrl($url = null)
    {
        if (!func_num_args()) {
            return static::$defaultBaseUrl;
        }
        static::$defaultBaseUrl = $url;
    }

    public static function defaultHeaders(array $headers = null)
    {
        if (!func_num_args()) {
            return static::$defaultHeaders;
        }

        static::$defaultHeaders = $headers;
    }

    public static function defaultParamsGet(array $params = null)
    {
        if (!func_num_args()) {
            return static::$defaultParamsGet;
        }

        static::$defaultParamsGet = $params;
    }

    public static function defaultParamsPost(array $params = null)
    {
        if (!func_num_args()) {
            return static::$defaultParamsPost;
        }

        static::$defaultParamsPost = $params;
    }

    public function curl()
    {
        if (!$this->curl) {
            $this->curl = curl_init();
        }
        
        return $this->curl;
    }
    public function baseUrl($url = null)
    {
        if (!func_num_args()) {
            return $this->baseUrl?: static::$defaultBaseUrl;
        }

        $this->baseUrl = $url;
        return $this;
    }

    public function headers(array $headers = null)
    {
        if (!func_num_args()) {
            return array_merge(static::$defaultHeaders, $this->headers);
        }

        $this->headers = $headers;
        return $this;
    }

    public function paramsGet(array $params = null)
    {
        if (!func_num_args()) {
            return array_replace(static::$defaultParamsGet, $this->paramsGet);
        }

        $this->paramsGet = $params;
        return $this;
    }

    public function paramsPost(array $params = null)
    {
        if (!func_num_args()) {
            return array_replace(static::$defaultParamsPost, $this->paramsPost);
        }

        $this->paramsPost = $params;
        return $this;
    }

    public function rawPostData($data = null)
    {
        if (!func_num_args()) {
            return $this->rawPostData;
        }

        $this->rawPostData = $data;
        return $this;
    }

    public function url($url = null)
    {
        if (!func_num_args()) {
            if (!preg_match('/^https?:\/\//', $this->url)) {
                return $this->baseUrl() . '/' . $this->url;
            }
            return $this->url;
        }

        $this->url = $url;
        return $this;
    }

    public function method($method = null)
    {
        if (!func_num_args()) {
            return $this->method;
        }

        $this->method = $method;
        return $this;
    }

    public function curlOption($name = null, $value = null)
    {
        if (!func_num_args()) {
            return array_replace(static::$defaultCurlOptions, $this->curlOptions);
        }

        if (is_array($name)) {
            $this->curlOptions = array_replace($this->curlOptions, $name);
        } else {
            $this->curlOptions[$name] = $value;
        }

        return $this;
    }

    public function send()
    {
        $curl = $this->curl();
        
        $url = $this->url();

        if ($paramsGet = $this->paramsGet()) {
            $urlParts = parse_url($url);

            $parts = [];
            if (isset($urlParts['scheme'])) {
                $parts['scheme'] = $urlParts['scheme'] . '://';
            }

            if (isset($urlParts['username'])) {
                $parts['username'] = $urlParts['username'] . ':';
            }

            if (isset($urlParts['password'])) {
                $parts['password'] = $urlParts['password'] . '@';
            }

            if (isset($urlParts['host'])) {
                $parts['host'] = $urlParts['host'];
            }

            if (isset($urlParts['path'])) {
                $parts['path'] = $urlParts['path'];
            }

            $params = array();
            if (isset($urlParts['query'])) {
                parse_str($urlParts['query'], $params);
            }
            $parts['query'] = '?' . http_build_query(array_merge($params, $paramsGet));

            $url = implode('', $parts);
        }

        $curlOptions = $this->curlOption();
        $curlOptions[CURLOPT_URL] = $url;
        $curlOptions[CURLOPT_HTTPHEADER] = $this->headers();

        $paramsPost = $this->paramsPost();

        if (!$paramsPost) {
            $rawPostData = $this->rawPostData;
        } else {
            $rawPostData = http_build_query($paramsPost);
        }

        if ($rawPostData) {
            $this->method('POST');
            $curlOptions[CURLOPT_POSTFIELDS] = $rawPostData;
        }

        if ($this->method() == 'POST') {
            $curlOptions[CURLOPT_POST] = true;
        }

        curl_setopt_array($curl, $curlOptions);

        $body = curl_exec($curl);

        if ($errNo = curl_errno($curl)) {
            $errMessage = curl_error($curl);
            curl_close($curl);
            throw new Exception($errMessage, $errNo);
        }

        $responseClass = $this->responseClass;
        $result = new $responseClass($body, curl_getinfo($curl, CURLINFO_HEADER_OUT));
        curl_close($curl);

        return $result;
    }
}