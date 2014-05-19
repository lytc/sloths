<?php

namespace Sloths\Http;

class Response extends AbstractMessage
{
    const CONTENT_TYPE_TEXT_HTML = 'text/html';
    const CONTENT_TYPE_APPLICATION_JSON = 'application/json';

    /**
     * @var int
     */
    protected $statusCode = 200;


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
     * @param string $url
     * @param int [$code=403]
     * @return $this
     */
    public function redirect($url, $code = 403)
    {
        $this->setStatusCode($code);
        $this->setHeader('Location', $url);
        return $this;
    }

    protected function sendHeader($name, $value)
    {
        header("$name: $value");
    }

    /**
     * @return $this
     */
    public function send()
    {
        http_response_code($this->statusCode);

        if ($redirectUrl = $this->getHeader('Location')) {
            $this->sendHeader('Location', $redirectUrl);
            return $this;
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

        foreach ($this->headers as $name => $value) {
            $this->sendHeader($name, $value);
        }

        echo $body;

        return $this;
    }
}