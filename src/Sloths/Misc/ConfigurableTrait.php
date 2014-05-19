<?php

namespace Sloths\Misc;

trait ConfigurableTrait
{
    /**
     * @var Config
     */
    protected $_config;

    /**
     * @return Config
     */
    public function getConfig()
    {
        if (!$this->_config) {
            $this->_config = new Config();
        }

        return $this->_config;
    }

    /**
     * @param Config $config
     * @return $this
     */
    public function setConfig(Config $config)
    {
        $this->_config = $config;
        return $this;
    }
}