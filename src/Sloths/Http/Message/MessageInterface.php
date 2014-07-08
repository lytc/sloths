<?php

namespace Sloths\Http\Message;

interface MessageInterface
{
    /**
     * @return string
     */
    public function getProtocolVersion();

    /**
     * @param array|Headers $headers
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function setHeaders($headers);

    /**
     * @return Headers
     */
    public function getHeaders();

    /**
     * @param $body
     * @return $this
     */
    public function setBody($body);

    /**
     * @return mixed
     */
    public function getBody();
}