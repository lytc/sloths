<?php

namespace Sloths\View\Helper;

use Sloths\View\View;
use SlothsTest\TestCase;

/**
 * @covers Sloths\View\Helper\Partial
 */
class PartialTest extends TestCase
{
    public function test()
    {
        $view = new View();
        $view->setDirectory(__DIR__ . '/fixtures');

        $result = $view->render('test-partial', ['foo' => 'foo']);

        $this->assertSame('template partial foo bar', $result);
    }
}