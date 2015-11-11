<?php

namespace SlothsTest\Application\Service;

/**
 * @covers Sloths\Application\Service\View
 */
class ViewTest extends TestCase
{
    public function testBoot()
    {
        $application = $this->getMock('app', ['getResourcePath']);
        $application->expects($this->once())->method('getResourcePath')->with('views')->willReturn('foo');

        $view = $this->getMock('Sloths\Application\Service\View', ['getApplication', 'setDirectory']);
        $view->expects($this->once())->method('getApplication')->willReturn($application);
        $view->expects($this->once())->method('setDirectory')->with('foo');

        $view->boot();
    }
}