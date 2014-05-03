<?php

namespace LazyTest\View\Helper;

use Lazy\View\View;

class MailToTest extends \PHPUnit_Framework_TestCase
{
    public function testMailTo()
    {
        $view = new View();

        $expected = 'mailto:foo%20name(foo@test.com)?cc=baz(baz@test.com)&subject=mail%20to%20subject&body=mail%20to%20body';
        $actual = $view->mailTo('foo@test.com', 'foo name', [
            'subject'   => 'mail to subject',
            'body'      => 'mail to body',
            'cc'        => [
                'baz@test.com' => 'baz'
            ]
        ]);

        $this->assertSame($expected, $actual);

        $expected = 'mailto:foo%20name(foo@test.com),bar@test.com?cc=baz(baz@test.com),buz@test.com&bcc=quz(quz@test.com)&subject=mail%20to%20subject&body=mail%20to%20body';
        $actual = $view->mailTo(['foo@test.com' => 'foo name', 'bar@test.com'], [
            'subject'   => 'mail to subject',
            'body'      => 'mail to body',
            'cc'        => [
                'baz@test.com' => 'baz',
                'buz@test.com'
            ],
            'bcc' => 'quz(quz@test.com)'
        ]);

        $this->assertSame($expected, $actual);
    }
}