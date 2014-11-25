<?php

namespace Sloths\Pagination\Adapter;

class ArrayAdapter implements AdapterInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * @param int $from
     * @param int $length
     * @return array
     */
    public function getRange($from, $length)
    {
        return array_slice($this->data, $from, $length);
    }
}