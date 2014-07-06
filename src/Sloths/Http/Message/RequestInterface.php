<?php

namespace Sloths\Http\Message;

interface RequestInterface extends MessageInterface
{
    /**
     * @param string $method
     * @throws \InvalidArgumentException If un supported method
     * @return mixed
     */
    public function setMethod($method);

    /**
     * @return string
     */
    public function getMethod();

    /**
     * @param string $url
     * @throws \InvalidArgumentException If the URL is invalid
     * @return $this
     */
    public function setUrl($url);

    /**
     * @return string
     */
    public function getUrl();
}