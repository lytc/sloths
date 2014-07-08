<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\OneOf;

class OneOfTest extends TestCase
{
    public function testOneOfRuleValidShouldReturnTrue()
    {
        $rule1 = $this->getMock('Sloths\Validation\ValidatableInterface', ['validate']);
        $rule2 = $this->getMock('Sloths\Validation\ValidatableInterface', ['validate']);

        $rule1->expects($this->once())->method('validate')->with('foo')->willReturn(false);
        $rule2->expects($this->once())->method('validate')->with('foo')->willReturn(true);

        $oneOf = new OneOf([$rule1, $rule2]);
        $this->assertTrue($oneOf->validate('foo'));
    }

    public function testAllOfRuleFailedShouldReturnFalseFalse()
    {
        $rule1 = $this->getMock('Sloths\Validation\ValidatableInterface', ['validate']);
        $rule2 = $this->getMock('Sloths\Validation\ValidatableInterface', ['validate']);

        $rule1->expects($this->once())->method('validate')->with('foo')->willReturn(false);
        $rule2->expects($this->once())->method('validate')->with('foo')->willReturn(false);

        $oneOf = new OneOf([$rule1, $rule2]);
        $this->assertFalse($oneOf->validate('foo'));
    }

    public function testValidateEmptyValueShouldReturnTrue()
    {
        $rule1 = $this->getMock('Sloths\Validation\ValidatableInterface', ['validate']);
        $rule1->expects($this->never())->method('validate');

        $oneOf = new OneOf([$rule1]);
        $this->assertTrue($oneOf->validate(null));
    }

    public function testValidateEmptyShouldReturnFalseIfRequired()
    {
        $rule1 = $this->getMock('Sloths\Validation\ValidatableInterface', ['validate']);
        $rule1->expects($this->never())->method('validate');

        $oneOf = new OneOf([$rule1]);
        $oneOf->required();
        $this->assertFalse($oneOf->validate(null));
        $this->assertInstanceOf('Sloths\Validation\Rule\Required', $oneOf->getFailedRules()[0]);
    }
}