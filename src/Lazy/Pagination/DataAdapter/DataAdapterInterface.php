<?php

namespace Lazy\Pagination\DataAdapter;

interface DataAdapterInterface
{
    /**
     * @return mixed
     */
    public function count();

    /**
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    public function items($limit, $offset);
}