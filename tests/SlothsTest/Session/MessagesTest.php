<?php

namespace SlothsTest\Session;

use Sloths\Session\Flash;
use Sloths\Session\Messages;
use SlothsTest\TestCase;

/**
 * @covers \Sloths\Session\Messages
 */
class MessagesTest extends TestCase
{
    public function testMethodAdd()
    {
        $flashSession = new Flash('foo');
        $messages = new Messages($flashSession);
        $messages->add($messages::ERROR, 'error');
        $messages->add($messages::INFO, 'info');

        $expected = [
            ['type' => $messages::ERROR, 'message' => 'error'],
            ['type' => $messages::INFO, 'message' => 'info'],
        ];
        $this->assertSame($expected, $flashSession->getNextData());
        $this->assertSame(count($messages), count($flashSession));
    }

    public function test()
    {
        $messages = new Messages();

        $messages->success('success');
        $messages->info('info');
        $messages->warning('warning');
        $messages->error('error');

        $expected = [
            ['type' => $messages::SUCCESS, 'message' => 'success'],
            ['type' => $messages::INFO, 'message' => 'info'],
            ['type' => $messages::WARNING, 'message' => 'warning'],
            ['type' => $messages::ERROR, 'message' => 'error'],
        ];

        $messages->now();
        $data = [];
        foreach ($messages as $message) {
            $data[] = $message;
        }

        $this->assertSame($expected, $data);
    }

    public function testNowKeepAndClear()
    {
        $flash = $this->getMock('Sloths\Session\Flash', ['now', 'keep', 'clear']);
        $flash->expects($this->once())->method('now');
        $flash->expects($this->once())->method('keep');
        $flash->expects($this->once())->method('clear');

        $messages = new Messages($flash);
        $this->assertSame($flash, $messages->getFlashSession());
        $messages->now()->keep()->clear();
    }
}