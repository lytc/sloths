<?php

namespace SlothsTest\View\Helper;

use Sloths\View\View;

/**
 * @covers \Sloths\View\Helper\MailTo
 */
class MailToTest extends \PHPUnit_Framework_TestCase
{
    public function testMailTo()
    {
        $view = new View();

        $this->assertSame('<a href="mailto:foo@test.com">foo@test.com</a>', (String) $view->mailTo('foo@test.com'));

        $expected = '<a href="mailto:foo%20name(foo@test.com)?cc=baz%28baz%40test.com%29&amp;subject=mail%20to%20subject&amp;body=mail%20to%20body">foo name(foo@test.com)</a>';
        $actual = (String) $view->mailTo('foo@test.com', 'foo name', [
            'subject'   => 'mail to subject',
            'body'      => 'mail to body',
            'cc'        => [
                'baz@test.com' => 'baz'
            ]
        ]);

        $this->assertSame($expected, $actual);

        $expected = '<a href="mailto:foo%20name(foo@test.com),bar@test.com?cc=baz%28baz%40test.com%29%2Cbuz%40test.com&amp;bcc=quz%28quz%40test.com%29&amp;subject=mail%20to%20subject&amp;body=mail%20to%20body">foo name(foo@test.com),bar@test.com</a>';
        $actual = (String) $view->mailTo(['foo@test.com' => 'foo name', 'bar@test.com'], [
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