<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Translation\Translator;

class AbstractCompositeTest extends TestCase
{
    public function testAddRule()
    {
        $collection = $this->getMockForAbstractClass('Sloths\Validation\Rule\AbstractComposite');

        $rule = $this->getMock('Sloths\Validation\ValidatableInterface');
        $collection->add($rule);
        $this->assertCount(1, $collection);
        $this->assertSame([$rule], $collection->getRules());

        $collection->add('string');
        $this->assertCount(2, $collection);
        $this->assertInstanceOf('Sloths\Validation\Rule\String', $collection->getRules()[1]);

        $collection->add('length', 3);
        $this->assertCount(3, $collection);
        $this->assertInstanceOf('Sloths\Validation\Rule\Length', $collection->getRules()[2]);

        $collection->add('length', [3]);
        $this->assertCount(4, $collection);
        $this->assertInstanceOf('Sloths\Validation\Rule\Length', $collection->getRules()[3]);

        $collection->add('length:3');
        $this->assertCount(5, $collection);
        $this->assertInstanceOf('Sloths\Validation\Rule\Length', $collection->getRules()[4]);

    }

    public function testAddRules()
    {
        $collection = $this->getMockForAbstractClass('Sloths\Validation\Rule\AbstractComposite');
        $rule = $this->getMock('Sloths\Validation\ValidatableInterface');

        $collection->addRules([$rule]);
        $this->assertCount(1, $collection);

        $collection->addRules(['length' => 3]);
        $this->assertCount(2, $collection);

        $collection->addRules('length:3');
        $this->assertCount(3, $collection);

        $collection->addRules('string|length:3');
        $this->assertCount(5, $collection);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddInvalidRuleShouldThrowAnException()
    {
        $collection = $this->getMockForAbstractClass('Sloths\Validation\Rule\AbstractComposite');
        $collection->add(new \stdClass());
    }

    public function testChain()
    {
        $collection = $this->getMockForAbstractClass('Sloths\Validation\Rule\AbstractComposite');
        $collection->email()->length(10);

        $this->assertCount(2, $collection);
    }

    public function testSetTranslatorShouldApplyForRules()
    {
        $collection = $this->getMock('Sloths\Validation\Rule\AbstractComposite', ['validate']);
        $translator = new Translator();

        $rule1 = $this->getMockForAbstractClass('Sloths\Validation\Rule\AbstractRule');
        $rule2 = $this->getMockForAbstractClass('Sloths\Validation\Rule\AbstractRule');

        $collection->add($rule1);
        $collection->setTranslator($translator);
        $this->assertSame($translator, $collection->getTranslator());

        $collection->add($rule2);
        $this->assertCount(2, $collection);

        $this->assertSame($translator, $rule1->getTranslator());
        $this->assertSame($translator, $rule2->getTranslator());
    }
}