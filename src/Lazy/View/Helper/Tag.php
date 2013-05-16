<?php

namespace Lazy\View\Helper;

class Tag extends AbstractHelper
{
    public function tag($name, array $attributes = []) {
        $attrs = [];
        foreach ($attributes as $key => $value) {
            $attrs[] = sprintf('%s="%s"', htmlentities($key), htmlentities($value));
        }

        $pattern = '<%s %s>';
        if (!in_array($name, ['area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen',
            'link', 'meta', 'param', 'source', 'track', 'wbr'])) {
            $pattern .= '</%s>';
        }

        return sprintf($pattern, $name, implode(' ', $attrs), $name);
    }
}