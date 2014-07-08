<?php

namespace Sloths\Http\Message;

interface ResponseInterface extends MessageInterface
{
    /**
     * @return int
     */
    public function getStatusCode();

    /**
     * @return string|null
     */
    public function getReasonPhrase();
}