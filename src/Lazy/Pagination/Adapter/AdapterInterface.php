<?php

namespace Lazy\Pagination\Adapter;

interface AdapterInterface
{
    public function count();
    public function items($offset, $limit);
}