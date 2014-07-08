<?php

namespace SlothsTest\Validation\Rule;

class AbstractRuleTest extends TestCase
{
    public function testCustomMessage()
    {
        $rule = $this->getMockForAbstractClass('Sloths\Validation\Rule\AbstractRule');

        $rule->setMessage('foo');
        $this->assertSame('foo', $rule->getMessage());

        $rule->setMessage(function() use (&$scope) {
            $scope = $this;
            return 'bar';
        });

        $this->assertSame('bar', $rule->getMessage());
        $this->assertSame($rule, $scope);
    }
}