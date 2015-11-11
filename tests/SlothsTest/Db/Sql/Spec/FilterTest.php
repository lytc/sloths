<?php

namespace SlothsTest\Db\Sql\Spec;

use Sloths\Db\Sql\Spec\Filter;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Db\Sql\Spec\Filter
 */
class FilterTest extends TestCase
{
    public function test()
    {
        $filter = new Filter();

        $filter
            ->add("c1 = 'c1'")
            ->and("c2 = ?", 'c2')
            ->or(["c3 = 'c3'", 'c4 = ?' => 'c4', 'c5' => 'c5'])
            ->add(function($filter) {
                $filter
                    ->add("c6 = 'c6'")
                    ->and("c7 = ?", 'c7')
                    ->or(['c8' => 'c8'])
                ;
            })
        ;

        $expected = "(c1 = 'c1') AND (c2 = 'c2') OR (c3 = 'c3' AND c4 = 'c4' AND `c5` = 'c5') AND ((c6 = 'c6') AND (c7 = 'c7') OR (`c8` = 'c8'))";
        $this->assertSame($expected, $filter->toString());

        $filter->reset();
        $this->assertSame('', $filter->toString());
    }

    public function testAnd()
    {
        $filter = $this->getMock('Sloths\Db\Sql\Spec\Filter', ['addFilter']);
        $filter->expects($this->once())->method('addFilter')->with('AND', 'condition', 'params');
        $filter->and('condition', 'params');
    }

    public function testOr()
    {
        $filter = $this->getMock('Sloths\Db\Sql\Spec\Filter', ['addFilter']);
        $filter->expects($this->once())->method('addFilter')->with('OR', 'condition', 'params');
        $filter->or('condition', 'params');
    }

    public function testAdd()
    {
        $filter = $this->getMock('Sloths\Db\Sql\Spec\Filter', ['addFilter']);
        $filter->expects($this->once())->method('addFilter')->with('AND', 'condition', 'params');
        $filter->add('condition', 'params');
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testCallUndefinedMethodShouldThrowAnException()
    {
        $filter = new Filter();
        $filter->foo();
    }
}