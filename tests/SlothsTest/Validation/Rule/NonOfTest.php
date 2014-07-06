<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\NoneOf;

class NoneOfTest extends TestCase
{
    public function testAllOfFailedShouldReturnTrue()
    {
        $rule1 = $this->getMock('Sloths\Validation\ValidatableInterface', ['validate']);
        $rule2 = $this->getMock('Sloths\Validation\ValidatableInterface', ['validate']);

        $rule1->expects($this->once())->method('validate')->with('foo')->willReturn(false);
        $rule2->expects($this->once())->method('validate')->with('foo')->willReturn(false);

        $oneOf = new NoneOf([$rule1, $rule2]);
        $this->assertTrue($oneOf->validate('foo'));
    }

    public function testOneOfValidShouldReturnFalse()
    {
        $rule1 = $this->getMock('Sloths\Validation\ValidatableInterface', ['validate']);
        $rule2 = $this->getMock('Sloths\Validation\ValidatableInterface', ['validate']);

        $rule1->expects($this->once())->method('validate')->with('foo')->willReturn(false);
        $rule2->expects($this->once())->method('validate')->with('foo')->willReturn(true);

        $oneOf = new NoneOf([$rule1, $rule2]);
        $this->assertFalse($oneOf->validate('foo'));
    }

    public function testValidateEmptyValueShouldReturnTrue()
    {
        $rule1 = $this->getMock('Sloths\Validation\ValidatableInterface', ['validate']);
        $rule1->expects($this->never())->method('validate');

        $noneOf = new NoneOf([$rule1]);
        $this->assertTrue($noneOf->validate(null));
    }

    public function testValidateEmptyShouldReturnFalseIfRequired()
    {
        $rule1 = $this->getMock('Sloths\Validation\ValidatableInterface', ['validate']);
        $rule1->expects($this->never())->method('validate');

        $noneOf = new NoneOf([$rule1]);
        $noneOf->required();
        $this->assertFalse($noneOf->validate(null));
        $this->assertInstanceOf('Sloths\Validation\Rule\Required', $noneOf->getFailedRules()[0]);
    }
}