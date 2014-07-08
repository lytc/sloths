<?php

namespace Sloths\Authentication\Adapter;

interface AdapterInterface
{
    /**
     * @return \Sloths\Authentication\Result
     */
    public function authenticate();
}