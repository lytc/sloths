<?php

namespace Sloths\Pagination\DataAdapter;

class ArrayAdapter implements DataAdapterInterface
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
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function items($limit, $offset)
    {
        return array_slice($this->data, $offset, $limit);
    }
}