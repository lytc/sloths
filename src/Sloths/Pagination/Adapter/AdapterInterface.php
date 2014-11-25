<?php

namespace Sloths\Pagination\Adapter;

interface AdapterInterface
{
    /**
     * @return int
     */
    public function count();

    /**
     * @param int $from
     * @param int $to
     * @return \Traversable
     */
    public function getRange($from, $to);
}