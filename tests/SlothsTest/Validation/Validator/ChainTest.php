<?php

namespace SlothsTest\Validation\Validator;

use Sloths\Validation\Validator\Required;
use SlothsTest\TestCase;
use Sloths\Validation\Validator\Chain;


/**
 * @covers Sloths\Validation\Validator\Chain
 */
class ChainTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, array $validators)
    {
        $chain = new Chain();
        $chain->addValidators($validators);

        $this->assertSame($expected, $chain->validate('foo'));
    }

    public function dataProvider()
    {
        $validator1 = $this->getMock('Sloths\Validation\Validator\ValidatorInterface');
        $validator1->expects($this->any())->method('validate')->with('foo')->willReturn(true);

        $validator2 = $this->getMock('Sloths\Validation\Validator\ValidatorInterface');
        $validator2->expects($this->any())->method('validate')->with('foo')->willReturn(true);

        $validator3 = $this->getMock('Sloths\Validation\Validator\ValidatorInterface');
        $validator3->expects($this->any())->method('validate')->with('foo')->willReturn(false);

        return [
            [true, [$validator1, $validator2]],
            [$validator3, [$validator1, $validator3]],
            [$validator3, [$validator3, $validator1]],
        ];
    }

    public function testRequired()
    {
        $chain = new Chain();
        $this->assertTrue($chain->validate(''));

        $chain->required();
        $this->assertInstanceOf('Sloths\Validation\Validator\Required', $chain->validate(''));
    }

    public function testAddRequiredRuleShouldFlagRequired()
    {
        $chain = new Chain();
        $chain->add(new Required());

        $this->assertTrue($chain->isRequired());;
    }

    public function testFromArray()
    {
        $chain = Chain::fromArray([
            $validator1 = $this->getMock('Sloths\Validation\Validator\ValidatorInterface'),
            'email',
            'numberBetween' => [1, 2],
            'greaterThan' => 3
        ]);

        $validators = $chain->getValidators();
        $this->assertCount(4, $validators);
        $this->assertSame($validator1, $validators[0]);
        $this->assertInstanceOf('Sloths\Validation\Validator\Email', $validators[1]);
        $this->assertInstanceOf('Sloths\Validation\Validator\NumberBetween', $validators[2]);
        $this->assertInstanceOf('Sloths\Validation\Validator\GreaterThan', $validators[3]);
    }
}