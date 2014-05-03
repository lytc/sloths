<?php

namespace LazyTest\View\Helper;

use Lazy\View\View;
use Lazy\View\Helper\FormatDate;

class FormatDateTest extends \PHPUnit_Framework_TestCase
{
    public function testFromString()
    {
        $view = new View();
        $expected = 'March 24, 2014';
        $this->assertSame($expected, (String) $view->formatDate('2014-03-24'));
    }

    public function testFromNumber()
    {
        $time = time();
        $view = new View();

        $expected = date(FormatDate::getDefaultOutputFormat(), $time);
        $this->assertSame($expected, (String) $view->formatDate($time));
    }

    public function testFromDateTimeClass()
    {
        $view = new View();
        $dateTime = new \DateTime();

        $expected = $dateTime->format(FormatDate::getDefaultOutputFormat());
        $this->assertSame($expected, (String) $view->formatDate($dateTime));
    }

    public function testItShouldReturnAnEmptyStringIfIsInvalidValue()
    {
        $view = new View();
        $this->assertSame('', (String) $view->formatDate(null));
        $this->assertSame('', (String) $view->formatDate('invalid date time'));
    }
}