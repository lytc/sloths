<?php

namespace Sloths\View\Helper;

class PartialLoop extends Partial
{
    public function partialLoop($file, $values)
    {
        $result = [];
        foreach ($values as $value) {
            $result[] = $this->partial($file, $value);
        }

        return implode('', $result);
    }
}