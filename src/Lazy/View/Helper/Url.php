<?php

namespace Lazy\View\Helper;

class Url extends AbstractHelper
{
    public function url($path, array $params) {
        return $path . '?' . http_build_query($params);
    }
}