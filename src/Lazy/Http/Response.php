<?php

namespace Lazy\Http;

class Response
{
    const CONTENT_TYPE_TEXT_HTML = 'text/html';
    const CONTENT_TYPE_APPLICATION_JSON = 'application/json';

    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var string|array
     */
    protected $body = '';

    /**
     * @param int $code
     * @return $this
     */
    public function setStatusCode($code)
    {
        $this->statusCode = (int) $code;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function addHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    /**
     * @param string $name
     * @param string [$value]
     * @return $this
     */
    public function setHeader($name, $value = null)
    {
        if (!$value) {
            $this->headers[] = $name;
        } else {
            $this->headers[$name] = $value;
        }

        return $this;
    }

    /**
     * @param string $contentType
     * @return $this
     */
    public function setContentType($contentType)
    {
        return $this->setHeader('Content-Type', $contentType);
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->getHeader('Content-Type');
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasHeader($name)
    {
        return isset($this->headers[$name]);
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function getHeader($name)
    {
        return $this->hasHeader($name)? $this->headers[$name] : null;
    }

    /**
     * @param string|array $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return array|string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function redirect($url, $code = 403)
    {
        $this->setStatusCode($code);
        $this->setHeader('Location', $url);
        return $this;
    }

    /**
     * @return $this
     */
    public function send()
    {
        http_response_code($this->statusCode);
        if ($redirectUrl = $this->getHeader('Location')) {
            header('Location: ' . $redirectUrl);
            exit;
        }

        $body = $this->body;
        if (is_array($body) || is_object($body)) {
            if (!$this->getContentType()) {
                $this->setContentType(self::CONTENT_TYPE_APPLICATION_JSON);
            }
            $body = json_encode($body);
        } else {
            if (!$this->getContentType()) {
                $this->setContentType(self::CONTENT_TYPE_TEXT_HTML);
            }
        }

        if (PHP_SAPI != 'cli') {
            foreach ($this->headers as $key => $value) {
                if (is_numeric($key)) {
                    header($value);
                } else {
                    header("$key: $value");
                }
            }
        }

        echo $body;

        return $this;
    }
}