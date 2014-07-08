<?php

namespace Sloths\Http\Message;

/**
 * @property Headers headers
 * @property mixed body
 */
abstract class AbstractMessage implements MessageInterface
{
    const PROTOCOL_VERSION_1_0 = '1.0';
    const PROTOCOL_VERSION_1_1 = '1.1';
    const DEFAULT_PROTOCOL_VERSION = self::PROTOCOL_VERSION_1_1;

    /**
     * @var string
     */
    protected $protocolVersion = self::DEFAULT_PROTOCOL_VERSION;

    /**
     * @var Headers
     */
    protected $headers;

    /**
     * @var mixed
     */
    protected $body;

    /**
     * @return string
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * @param array|Headers $headers
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function setHeaders($headers)
    {
        if (is_array($headers)) {
            $headers = new Headers($headers);
        }

        if (!$headers instanceof Headers) {
            throw new \InvalidArgumentException(
                sprintf('Headers must be an array or instance of %s\Headers, %s given', __NAMESPACE__, gettype($headers))
            );
        }

        $this->headers = $headers;
        return $this;
    }

    /**
     * @return Headers
     */
    public function getHeaders()
    {
        if (!$this->headers) {
            $this->headers = new Headers();
        }

        return $this->headers;
    }

    /**
     * @param mixed $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case 'headers': return $this->setHeaders($value);
            case 'body': return $this->setBody($value);
        }

        throw new \BadMethodCallException(sprintf('Call to undefined property %s', $name));
    }

    /**
     * @param string $name
     * @return Parameters
     * @throws \BadMethodCallException
     */
    public function __get($name)
    {
        switch ($name) {
            case 'headers': return $this->getHeaders();
            case 'body': return $this->getBody();
        }

        throw new \BadMethodCallException(sprintf('Call to undefined property %s', $name));
    }
}