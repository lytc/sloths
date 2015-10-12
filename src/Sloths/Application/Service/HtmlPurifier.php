<?php

namespace Sloths\Application\Service;

class HtmlPurifier extends \HTMLPurifier implements ServiceInterface
{
    use ServiceTrait;

    public function getConfig()
    {
        return $this->config;
    }
}