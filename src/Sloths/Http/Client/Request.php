<?php

namespace Sloths\Http\Client;

use Sloths\Http\AbstractRequest;

class Request extends AbstractRequest
{
    /**
     * @param string $method
     * @param string $url
     * @param array $params
     * @param array $headers
     */
    public function __construct($method = self::METHOD_GET, $url = null, array $params = null, array $headers = null)
    {
        $this->setMethod($method);

        if ($url) {
            $this->setUrl($url);
        }

        if ($params) {
            $this->setParams($params);
        }

        if ($headers) {
            $this->setHeaders($headers);
        }
    }
}