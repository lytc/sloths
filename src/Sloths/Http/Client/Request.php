<?php

namespace Sloths\Http\Client;

use Sloths\Http\Message\AbstractRequest;
use Sloths\Util\UrlUtils;

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

    /**
     * @param array|\Sloths\Http\Message\Parameters $files
     * @return $this
     */
    public function setFileParams($files)
    {
        if (!is_array($files)) {
            $files = [$files];
        }

        return parent::setFileParams($files);
    }
}