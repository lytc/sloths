<?php

namespace Sloths\Misc;

interface ConfigurableInterface
{
    /**
     * @param string|array $files
     * @return $this
     */
    public function loadConfigFromFile($files);
}