<?php

namespace SlothsTest\Encryption\Password;

use Sloths\Encryption\Password\Md5;
use SlothsTest\TestCase;

class Md5Test extends TestCase
{
    public function test()
    {
        $encrypt = new Md5();
        $this->assertTrue($encrypt->verify('test', md5('test')));
    }
}