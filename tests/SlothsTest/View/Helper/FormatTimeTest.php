<?php

namespace SlothsTest\View\Helper;

use Sloths\View\View;
use Sloths\View\Helper\FormatTime;

/**
 * @covers \Sloths\View\Helper\FormatTime<extended>
 */
class FormatTimeTest extends \PHPUnit_Framework_TestCase
{
    public function testFromString()
    {
        $view = new View();
        $expected = '03:18:24 PM';
        $this->assertSame($expected, (String) $view->formatTime('15:18:24'));
    }

    public function testFromNumber()
    {
        $time = time();
        $view = new View();

        $expected = date(FormatTime::getDefaultOutputFormat(), $time);
        $this->assertSame($expected, (String) $view->formatTime($time));
    }

    public function testFromDateTimeClass()
    {
        $view = new View();
        $dateTime = new \DateTime();

        $expected = $dateTime->format(FormatTime::getDefaultOutputFormat());
        $this->assertSame($expected, (String) $view->formatTime($dateTime));
    }

    public function testItShouldReturnAnEmptyStringIfIsInvalidValue()
    {
        $view = new View();
        $this->assertSame('', (String) $view->formatTime(null));
        $this->assertSame('', (String) $view->formatTime('invalid date time'));
    }
}