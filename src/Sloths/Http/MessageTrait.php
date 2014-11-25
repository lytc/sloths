<?php

namespace Sloths\Http;

trait MessageTrait
{
    /**
     * @var string
     */
    protected $protocolVersion = MessageInterface::DEFAULT_PROTOCOL_VERSION;

    /**
     * @var \Sloths\Http\Headers
     */
    protected $headers;

    /**
     * @var mixed
     */
    protected $body;

    /**
     * @param string $version
     * @return $this
     */
    public function setProtocolVersion($version)
    {
        $this->protocolVersion = $version;
        return $this;
    }

    /**
     * @return string
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * @param \Sloths\Http\Headers|array $headers
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setHeaders($headers)
    {
        if (is_array($headers)) {
            $headers = new Headers($headers);
        } else if (!$headers instanceof Headers) {
            throw new \InvalidArgumentException(sprintf(
                'Header must be an instance of Sloths\Http\Headers or an array. %s given', gettype($headers)));
        }

        $this->headers = $headers;
        return $this;
    }

    /**
     * @return \Sloths\Http\Headers
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
}