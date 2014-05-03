<?php

namespace LazyTest;

class TestCase extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        \Mockery::close();
    }

    /**
     * @return \Mockery\MockInterface
     */
    public function mock()
    {
        return call_user_func_array('\Mockery::mock', func_get_args());
    }
}