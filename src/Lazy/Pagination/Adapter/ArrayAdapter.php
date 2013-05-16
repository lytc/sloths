<?php

namespace Lazy\Pagination\Adapter;

class ArrayAdapter implements AdapterInterface
{
    protected $data;
    protected $count;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->count = count($data);
    }

    public function count()
    {
        return $this->count;
    }

    public function items($offset, $limit)
    {
        return array_slice($this->data, $offset, $limit);
    }
}