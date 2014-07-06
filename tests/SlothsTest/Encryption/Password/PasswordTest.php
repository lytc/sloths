<?php

namespace SlothsTest\Encryption\Password;

use Sloths\Encryption\Password\Password;
use SlothsTest\TestCase;

class PasswordTest extends TestCase
{
    public function testVerify()
    {
        $encrypt = new Password();
        $this->assertTrue($encrypt->verify('test', password_hash('test', Password::ALGO_DEFAULT)));
        $this->assertTrue(password_verify('test', $encrypt->hash('test')));
    }

    public function testNeedsRehash()
    {
        $encrypt = new Password();
        $hash = $encrypt->hash('test');
        $this->assertFalse($encrypt->needsRehash($hash));

        $encrypt->setAlgorithm(Password::ALGO_BCRYPT)
            ->setSalt('1234567890qwerty')
            ->setCost(11);

        $this->assertTrue($encrypt->needsRehash($hash));
    }
}