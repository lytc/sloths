<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Not;

class NotTest extends TestCase
{
    public function test()
    {
        $rule1 = $this->getMock('Sloths\Validation\Rule\AbstractRule');
        $rule1->expects($this->once())->method('validate')->willReturn(true);

        $rule2 = $this->getMock('Sloths\Validation\Rule\AbstractRule');
        $rule2->expects($this->once())->method('validate')->willReturn(false);

        $this->assertFalse((new Not($rule1))->validate(''));
        $this->assertTrue((new Not($rule2))->validate(''));
    }
}