<?php

namespace SlothsTest\Application\Service;

use Sloths\Application\Application;
use SlothsTest\TestCase;

class ViewTest extends TestCase
{
    public function testApplicationContext()
    {
        $application = new Application();
        $application->setDirectory('foo');

        $view = $application->view;
        $this->assertSame('foo/views', $view->getDirectory());
    }
}