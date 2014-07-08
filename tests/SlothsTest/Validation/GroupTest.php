<?php

namespace SlothsTest\Validation;

use Sloths\Translation\Translator;
use Sloths\Validation\Group;
use Sloths\Validation\Rule\AllOf;
use SlothsTest\TestCase;

class GroupTest extends TestCase
{
    public function testMethodAdd()
    {
        $group = new Group();
        $rule = new AllOf();
        $group->add('foo', $rule);

        $this->assertTrue($group->has('foo'));
        $this->assertSame($rule, $group->get('foo'));
    }

    public function testMethodValidate()
    {
        $rule1 = $this->getMock('Sloths\Validation\Rule\AbstractComposite', ['validate']);
        $rule1->expects($this->once())->method('validate')->with('bar')->willReturn(true);

        $rule2 = $this->getMock('Sloths\Validation\Rule\AbstractComposite', ['validate', 'getMessages']);
        $rule2->expects($this->once())->method('validate')->with('baz')->willReturn(false);
        $rule2->expects($this->once())->method('getMessages')->willReturn(['foo' => 'bar']);

        $group = new Group(['foo' => $rule1, 'bar' => $rule2]);

        $isValid = $group->validate(['foo' => 'bar', 'bar' => 'baz']);
        $this->assertFalse($isValid);
        $this->assertSame(['bar' => $rule2], $group->getFailedItems());
        $this->assertSame(['bar' => ['foo' => 'bar']], $group->getMessages()->toArray());
    }

    public function testTranslator()
    {
        $translator = new Translator();
        $group = new Group();
        $group->setTranslator($translator);

        $rule1 = $this->getMock('Sloths\Validation\Rule\AbstractComposite', ['validate', 'setTranslator']);
        $rule1->expects($this->exactly(2))->method('setTranslator')->with($translator);

        $group->add('foo', $rule1);
        $group->setTranslator($translator);
    }

    public function testNewInstanceWithTranslator()
    {
        $translator = new Translator();
        $group = new Group([], $translator);

        $this->assertSame($translator, $group->getTranslator());
    }

    public function testAddRuleFromString()
    {
        $group = new Group([
            'foo' => 'email',
            'bar' => 'alnum'
        ]);
        $group->add('baz', 'numeric');

        $this->assertCount(3, $group);
        $this->assertCount(3, $group->getItems());

        $this->assertInstanceOf('Sloths\Validation\Rule\AllOf', $group->get('foo'));
        $this->assertInstanceOf('Sloths\Validation\Rule\AllOf', $group->get('bar'));
        $this->assertInstanceOf('Sloths\Validation\Rule\AllOf', $group->get('baz'));
    }
}