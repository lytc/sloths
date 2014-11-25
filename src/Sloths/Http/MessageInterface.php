<?php

namespace Sloths\Http;

interface MessageInterface
{
    const PROTOCOL_VERSION_1_0 = '1.0';
    const PROTOCOL_VERSION_1_1 = '1.1';
    const DEFAULT_PROTOCOL_VERSION = self::PROTOCOL_VERSION_1_1;

    /**
     * @return string
     */
    public function getProtocolVersion();

    /**
     * @param \Sloths\Http\Headers|array $headers
     * @return $this
     */
    public function setHeaders($headers);

    /**
     * @return \Sloths\Http\Headers
     */
    public function getHeaders();

    /**
     * @param mixed $body
     * @return $this
     */
    public function setBody($body);

    /**
     * @return mixed
     */
    public function getBody();
}