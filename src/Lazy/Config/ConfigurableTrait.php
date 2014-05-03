<?php

namespace Lazy\Config;

trait ConfigurableTrait
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @return Config
     */
    public function getConfig()
    {
        if (!$this->config) {
            $this->config = new Config();
        }

        return $this->config;
    }

    /**
     * @param Config $config
     * @return $this
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
        return $this;
    }
}