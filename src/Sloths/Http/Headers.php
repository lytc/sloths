<?php

namespace Sloths\Http;

use Sloths\Misc\Parameters;

class Headers extends Parameters
{
    /**
     * @param string $name
     * @return string
     */
    public static function processName($name)
    {
        $name = trim($name);
        $name = preg_split('/(-|_)/', $name);

        array_walk($name, function(&$item) {
            $item = ucfirst(strtolower($item));
        });

        return implode('-', $name);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function set($name, $value)
    {
        $name = static::processName($name);
        return parent::set($name, $value);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        $name = static::processName($name);
        return parent::has($name);
    }

    /**
     * @param string $name
     * @param null $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $name = static::processName($name);
        return parent::get($name, $default);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function remove($name)
    {
        $name = static::processName($name);
        return parent::remove($name);
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function getLine($name)
    {
        if (!$this->has($name)) {
            return null;
        }

        $name = static::processName($name);
        $value = $this->get($name);
        return "$name: $value";
    }

    /**
     * @return array
     */
    public function getLines()
    {
        $lines = [];

        foreach ($this as $name => $value) {
            $lines[] = "$name: $value";
        }

        return $lines;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return implode("\r\n", $this->getLines());
    }
}