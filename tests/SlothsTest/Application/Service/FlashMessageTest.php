<?php

namespace SlothsTest\Application\Service;

use Sloths\Application\Service\FlashMessage;

/**
 * @covers Sloths\Application\Service\FlashMessage
 */
class FlashMessageTest extends TestCase
{
    public function test()
    {
        $currentMessages = new \ArrayObject([
            ['type' => FlashMessage::SUCCESS, 'text' => 'current success'],
            ['type' => FlashMessage::INFO, 'text' => 'current info'],
            ['type' => FlashMessage::WARNING, 'text' => 'current warning'],
            ['type' => FlashMessage::DANGER, 'text' => 'current danger'],
            ['type' => FlashMessage::ERROR, 'text' => 'current error'],
        ]);

        $flash = $this->getMock('flash', ['get', 'set']);
        $flash->expects($this->once())->method('get')->with('messages')->willReturn($currentMessages);

        $session = $this->getMock('session', ['flash']);
        $session->expects($this->once())->method('flash')->willReturn($flash);

        $serviceManager = $this->getMock('serviceManager', ['get']);
        $serviceManager->expects($this->once())->method('get')->with('session')->willReturn($session);

        $application = $this->getMockApplication();
        $application->expects($this->once())->method('getServiceManager')->willReturn($serviceManager);

        $flashMessage = new FlashMessage();
        $flashMessage->setApplication($application);

        $this->assertSame($currentMessages, $flashMessage->getAll());
        $this->assertSame(['current success'], $flashMessage->getSuccesses());
        $this->assertSame(['current info'], $flashMessage->getInfo());
        $this->assertSame(['current warning'], $flashMessage->getWarnings());
        $this->assertSame(['current danger'], $flashMessage->getDangers());
        $this->assertSame(['current error'], $flashMessage->getErrors());

        $flashMessage->success('success');
        $flashMessage->info('info');
        $flashMessage->warning('warning');
        $flashMessage->danger('danger');
        $flashMessage->error('error');

        $expected = [
            ['type' => FlashMessage::SUCCESS, 'text' => 'success'],
            ['type' => FlashMessage::INFO, 'text' => 'info'],
            ['type' => FlashMessage::WARNING, 'text' => 'warning'],
            ['type' => FlashMessage::DANGER, 'text' => 'danger'],
            ['type' => FlashMessage::ERROR, 'text' => 'error'],
        ];

        $this->assertSame($expected, (array) $flashMessage->getNextMessages());
    }
}