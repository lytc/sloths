<?php

namespace Sloths\Misc;

trait ConfigurableTrait
{
    /**
     * @param string|array $files
     * @return $this
     */
    public function loadConfigFromFile($files)
    {
        if (!is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            require $file;
        }

        return $this;
    }
}