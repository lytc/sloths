<?php

namespace SlothsTest\Session;

use Sloths\Session\Flash;
use Sloths\Session\Messages;
use SlothsTest\TestCase;

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
        $messages = $this->getMock('Sloths\Session\Messages', ['add']);
        $messages->expects($this->at(0))->method('add')->with($messages::SUCCESS, 'success');
        $messages->expects($this->at(1))->method('add')->with($messages::INFO, 'info');
        $messages->expects($this->at(2))->method('add')->with($messages::WARNING, 'warning');
        $messages->expects($this->at(3))->method('add')->with($messages::ERROR, 'error');

        $messages->success('success');
        $messages->info('info');
        $messages->warning('warning');
        $messages->error('error');
    }
}