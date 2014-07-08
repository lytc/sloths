<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\AllOf;

class AllOfTest extends TestCase
{
    public function testAllOfValidShouldReturnTrue()
    {
        $rule1 = $this->getMock('Sloths\Validation\ValidatableInterface', ['validate']);
        $rule2 = $this->getMock('Sloths\Validation\ValidatableInterface', ['validate']);

        $rule1->expects($this->once())->method('validate')->with('foo')->willReturn(true);
        $rule2->expects($this->once())->method('validate')->with('foo')->willReturn(true);

        $allOf = new AllOf([$rule1, $rule2]);
        $this->assertTrue($allOf->validate('foo'));
    }

    public function testOneOfFailedShouldReturnFalse()
    {
        $rule1 = $this->getMock('Sloths\Validation\ValidatableInterface', ['validate']);
        $rule2 = $this->getMock('Sloths\Validation\ValidatableInterface', ['validate']);

        $rule1->expects($this->once())->method('validate')->with('foo')->willReturn(true);
        $rule2->expects($this->once())->method('validate')->with('foo')->willReturn(false);

        $allOf = new AllOf([$rule1, $rule2]);
        $this->assertFalse($allOf->validate('foo'));
    }
    
    public function testValidateEmptyValueShouldReturnTrue()
    {
        $rule1 = $this->getMock('Sloths\Validation\ValidatableInterface', ['validate']);
        $rule1->expects($this->never())->method('validate');

        $allOf = new AllOf([$rule1]);
        $this->assertTrue($allOf->validate(null));
    }

    public function testValidateEmptyShouldReturnFalseIfRequired()
    {
        $rule1 = $this->getMock('Sloths\Validation\ValidatableInterface', ['validate']);
        $rule1->expects($this->never())->method('validate');

        $allOf = new AllOf([$rule1]);
        $allOf->required();
        $this->assertFalse($allOf->validate(null));
        $this->assertInstanceOf('Sloths\Validation\Rule\Required', $allOf->getFailedRules()[0]);
    }

    public function testGetFailedRulesAndMessages()
    {
        $rule1 = $this->getMock('Sloths\Validation\ValidatableInterface', ['validate', 'getMessage']);
        $rule1->expects($this->once())->method('validate')->with('foo')->willReturn(true);
        $rule1->expects($this->never())->method('getMessage');

        $rule2 = $this->getMock('Sloths\Validation\ValidatableInterface', ['validate', 'getMessage']);
        $rule2->expects($this->once())->method('validate')->with('foo')->willReturn(false);
        $rule2->expects($this->once())->method('getMessage')->willReturn('message');

        $allOf = new AllOf([$rule1, $rule2]);
        $allOf->validate('foo');

        $this->assertSame([$rule2], $allOf->getFailedRules());
        $this->assertSame(['message'], $allOf->getMessages());
    }
}