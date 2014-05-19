<?php

namespace SlothsTest\Application\Service;

use Sloths\Application\Application;
use SlothsTest\TestCase;

class RouterTest extends TestCase
{
    public function testApplicationContext()
    {
        $application = new Application();
        $application->setDirectory('foo');

        $router = $application->router;
        $this->assertSame($application, $router->getContext());
        $this->assertSame('foo/routes', $router->getDirectory());
    }
}