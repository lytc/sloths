<?php

namespace LazyTest\View;
use Lazy\View\View;

class MailtoTest extends \PHPUnit_Framework_TestCase
{
    public function testMailto()
    {
        $view = new View();
        $this->assertSame('<a href="mailto:foo@test.com">foo@test.com</a>', $view->mailto('foo@test.com'));
    }
}