<?php

namespace Sloths\Http;

abstract class AbstractMessage
{
    protected $headers = [];
    protected $body;

    public static function processHeaderName($name)
    {
        $name = trim($name);
        $name = preg_split('/(-|_)/', $name);

        array_walk($name, function(&$item) {
            $item = ucfirst(strtolower($item));
        });

        return implode('-', $name);
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getHeaderAsString()
    {
        $result = [];

        foreach ($this->headers as $name => $value) {
            $result[] = sprintf('%s: %s', $name, $value);
        }

        return implode("\r\n", $result);
    }

    public function getHeader($name)
    {
        $name = static::processHeaderName($name);
        return array_key_exists($name, $this->headers)? $this->headers[$name] : null;
    }

    public function hasHeader($name)
    {
        $name = static::processHeaderName($name);
        return array_key_exists($name, $this->headers);
    }

    public function setHeader($name, $value)
    {
        $name = static::processHeaderName($name);
        $this->headers[$name] = $value;

        return $this;
    }

    public function setHeaders(array $headers)
    {
        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value);
        }

        return $this;
    }

    public function removeHeader($name)
    {
        $name = static::processHeaderName($name);
        unset($this->headers[$name]);

        return $this;
    }

    public function resetHeaders()
    {
        $this->headers = [];
        return $this;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }
}