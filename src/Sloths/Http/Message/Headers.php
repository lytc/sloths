<?php

namespace Sloths\Http\Message;

class Headers extends Parameters
{
    /**
     * @param string $name
     * @return string
     */
    public static function processHeaderName($name)
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
        $name = $this->processHeaderName($name);
        return parent::set($name, $value);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        $name = $this->processHeaderName($name);
        return parent::get($name);
    }

    /**
     * @param string $name
     */
    public function remove($name)
    {
        $name = $this->processHeaderName($name);
        return parent::remove($name);
    }

    /**
     * @param string $name
     * @return null|string
     */
    public function getLine($name)
    {
        return $this->has($name)? $name . ': ' . $this->get($name) : null;
    }

    /**
     * @return array
     */
    public function getLines()
    {
        $result = [];

        foreach ($this->params as $name => $value) {
            $result[] = $name . ': ' . $value;
        }

        return $result;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return implode("\r\n", $this->getLines());
    }
}