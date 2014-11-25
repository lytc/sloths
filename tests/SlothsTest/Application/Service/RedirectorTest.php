<?php

namespace SlothsTest\Application\Service;
use Sloths\Application\Service\Redirector;

/**
 * @covers Sloths\Application\Service\Redirector
 */
class RedirectorTest extends TestCase
{
    public function testTo()
    {
        $headers = $this->getMock('Headers', ['set']);
        $headers->expects($this->once())->method('set')->with('Location', 'url');

        $response = $this->getMock('Response', ['setStatusCode', 'getHeaders']);
        $response->expects($this->once())->method('setStatusCode')->with(302);
        $response->expects($this->once())->method('getHeaders')->willReturn($headers);

        $application = $this->getMockApplication();
        $application->expects($this->once())->method('getResponse')->willReturn($response);

        $redirector = new Redirector();
        $redirector->setApplication($application);

        $this->assertSame($response, $redirector->to('url'));
    }

    public function testBack()
    {
        $request = $this->getMock('Request', ['getReferrer']);
        $request->expects($this->once())->method('getReferrer')->willReturn('url');

        $application = $this->getMockApplication();
        $application->expects($this->once())->method('getRequest')->willReturn($request);

        $redirector = $this->getMock('Sloths\Application\Service\Redirector', ['to']);
        $redirector->setApplication($application);
        $redirector->expects($this->once())->method('to')->with('url');
        $redirector->back();
    }
}