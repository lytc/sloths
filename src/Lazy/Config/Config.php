<?php

namespace Lazy\Config;

class Config implements \IteratorAggregate, \Countable, \JsonSerializable
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @param $files
     * @param bool $replaceRecursive
     * @return static
     * @throws \InvalidArgumentException
     */
    public static function fromFile($files, $replaceRecursive = true)
    {
        is_array($files) || $files = [$files];

        $data = [];

        foreach ($files as $file) {
            $result = call_user_func(function() use ($file) {
                $extension = pathinfo($file, PATHINFO_EXTENSION);

                switch ($extension) {
                    case 'json':
                        return json_decode(file_get_contents($file), true);

                    case 'php':
                        return require $file;

                }

                throw new \InvalidArgumentException(sprintf('Config file should be php or json file. %s given', $extension));
            });

            if (!is_array($result)) {
                throw new \InvalidArgumentException('Config file should return an array');
            }

            if ($replaceRecursive) {
                $data = array_replace_recursive($data, $result);
            } else {
                $data = array_merge($data, $result);
            }
        }

        return new static($data);
    }

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->exchange($data);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * @return \Generator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    /**
     * @param $data
     */
    public function exchange($data)
    {
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $data[$k] = new static($v);
            }
        }

        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function toArray()
    {
        $data = $this->data;

        foreach ($data as $k => $v) {
            if ($v instanceof self) {
                $data[$k] = $v->toArray();
            }
        }

        return $data;
    }

    /**
     * @param Config $config
     * @return $this
     */
    public function merge(Config $config)
    {
        $this->exchange(array_merge($this->toArray(), $config->toArray()));
        return $this;
    }

    /**
     * @param Config $config
     * @return $this
     */
    public function replaceRecursive(Config $config)
    {
        $this->exchange(array_replace_recursive($this->toArray(), $config->toArray()));
        return $this;
    }

    /**
     * @param $k
     * @param $v
     */
    public function __set($k, $v)
    {
        if (is_array($v)) {
            $v = new static($v);
        }

        $this->data[$k] = $v;
    }

    /**
     * @param $k
     * @return null
     */
    public function __get($k)
    {
        return isset($this->data[$k])? $this->data[$k] : null;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}