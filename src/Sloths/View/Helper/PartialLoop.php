<?php

namespace Sloths\View\Helper;

class PartialLoop extends Partial
{
    /**
     * @param string $file
     * @param array $values
     * @return string
     */
    public function partialLoop($file, $values)
    {
        $result = [];
        foreach ($values as $value) {
            $result[] = $this->partial($file, $value);
        }

        return implode('', $result);
    }
}