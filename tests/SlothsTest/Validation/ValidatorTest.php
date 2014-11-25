<?php

namespace Sloths\Validation\Validator;

use SlothsTest\TestCase;
use Sloths\Validation\Validator;

/**
 * @covers Sloths\Validation\Validator
 */
class ValidatorTest extends TestCase
{
    public function testCreateRule()
    {
        $emailValidator = Validator::createRule('email');
        $this->assertInstanceOf('Sloths\Validation\Validator\Email', $emailValidator);
    }

    public function testCustomRule()
    {
        $foo = function() {};
        $bar = function() {};

        Validator::addRule('foo', $foo);
        Validator::addRules(['bar' => $bar]);

        $this->assertInstanceOf('Sloths\Validation\Validator\Callback', Validator::createRule('foo'));
        $this->assertInstanceOf('Sloths\Validation\Validator\Callback', Validator::createRule('bar'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateUndefinedRuleShouldThrowAnException()
    {
        Validator::createRule('undefinedRule');
    }

    public function testCreateRuleWithCallback()
    {
        $rule = Validator::createRule(function() {});
        $this->assertInstanceOf('Sloths\Validation\Validator\Callback', $rule);
    }

    public function testCreateRuleWithRuleInstance()
    {
        $rule = $this->getMock('Sloths\Validation\Validator\ValidatorInterface');
        $this->assertSame($rule, Validator::createRule($rule));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateRuleWithInvalidArgShouldThrowAnException()
    {
        Validator::createRule(new \stdClass());
    }

    /**
     * @dataProvider dataProviderTestValidate
     */
    public function testValidate($expected, $chains, $data)
    {
        $validator = new Validator();
        $validator->addChains($chains);

        $this->assertSame($expected, $validator->validate($data));
    }

    public function dataProviderTestValidate()
    {
        return [
            [
                true,
                [
                    'name' => 'required',
                    'email' => ['email'],
                    'age' => ['greaterThan' => 17]
                ],
                [
                    'name' => 'foo',
                    'email' => 'email@example.com',
                    'age' => 18
                ]
            ],
            [
                true,
                [
                    'name' => 'required',
                    'email' => ['email'],
                    'age' => ['greaterThan' => 17]
                ],
                [
                    'name' => 'foo',
                    'email' => '',
                    'age' => ''
                ]
            ],
            [
                false,
                [
                    'name' => 'required',
                    'email' => ['email'],
                    'age' => ['greaterThan' => 17]
                ],
                [
                    'name' => '',
                    'email' => 'foo',
                    'age' => '16'
                ]
            ]
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testValidateWithInvalidDataInputShouldThrowAnException()
    {
        (new Validator())->validate(1);
    }

    public function testFails()
    {
        $rule1 = $this->getMock('Sloths\Validation\Validator\ValidatorInterface');
        $rule1->expects($this->once())->method('getMessageTemplate')->willReturn('message 1 :foo');
        $rule1->expects($this->once())->method('getDataForMessage')->willReturn(['foo' => 'foo']);

        $rule2 = $this->getMock('Sloths\Validation\Validator\ValidatorInterface');
        $rule2->expects($this->once())->method('getMessageTemplate')->willReturn('message 2 :bar');
        $rule2->expects($this->once())->method('getDataForMessage')->willReturn(['bar' => 'bar']);

        $chain1 = $this->getMock('Sloths\Validation\Validator\Chain', ['validate']);
        $chain1->expects($this->once())->method('validate')->willReturn($rule1);

        $chain2 = $this->getMock('Sloths\Validation\Validator\Chain', ['validate']);
        $chain2->expects($this->once())->method('validate')->willReturn(true);

        $chain3 = $this->getMock('Sloths\Validation\Validator\Chain', ['validate']);
        $chain3->expects($this->once())->method('validate')->willReturn($rule2);

        $validator = new Validator();
        $validator->addChains(['foo' => $chain1, 'bar' => $chain2, 'baz' => $chain3]);

        $this->assertFalse($validator->validate([]));
        $this->assertSame(['foo' => $rule1, 'baz' => $rule2], $validator->fails());

        $this->assertSame([
            'foo' => 'message 1 foo',
            'baz' => 'message 2 bar'
        ], $validator->getMessages());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFailsShouldThrowAnExceptionIfNotValidated()
    {
        (new Validator())->fails();
    }

    public function testTranslator()
    {
        $validator = $this->getMock('Sloths\Validation\Validator', ['getMessageTemplates', 'getTranslator']);
        $validator->expects($this->once())->method('getMessageTemplates')->willReturn([
            'foo' => [
                'template' => 'template 1 :foo',
                'data' => ['foo' => 'foo']
            ]
        ]);

        $translator = $this->getMock('translator', ['translate']);
        $translator->expects($this->once())->method('translate')->with('template 1 :foo', ['foo' => 'foo'])->willReturn('message translated');
        $validator->expects($this->once())->method('getTranslator')->willReturn($translator);

        $this->assertSame([
            'foo' => 'message translated'
        ], $validator->getMessages());
    }
}