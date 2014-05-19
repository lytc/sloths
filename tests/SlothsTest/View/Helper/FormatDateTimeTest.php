<?php

namespace SlothsTest\View\Helper;

use Sloths\View\View;
use Sloths\View\Helper\FormatDateTime;

class FormatDateTimeTest extends \PHPUnit_Framework_TestCase
{
    public function testSetAndGetInputOutputFormat()
    {
        $inputFormat = 'Y.m.d H:i:s';
        $outputFormat = 'Y-m-d H:i:s';
        $view = new View();
        $helper = $view->formatDateTime('2014.05.02 02:16:11');
        $helper->setInputFormat($inputFormat);
        $this->assertSame($inputFormat, $helper->getInputFormat());

        $helper->setOutputFormat($outputFormat);
        $this->assertSame($outputFormat, $helper->getOutputFormat());

        $this->assertSame('2014-05-02 02:16:11', $helper->__toString());
    }

    public function testFromString()
    {
        $view = new View();
        $expected = 'March 24, 2014 03:18:24 PM';
        $this->assertSame($expected, (String) $view->formatDateTime('2014-03-24 15:18:24'));
    }

    public function testFromNumber()
    {
        $time = time();
        $view = new View();

        $expected = date(FormatDateTime::getDefaultOutputFormat(), $time);
        $this->assertSame($expected, (String) $view->formatDateTime($time));
    }

    public function testFromDateTimeClass()
    {
        $view = new View();
        $dateTime = new \DateTime();

        $expected = $dateTime->format(FormatDateTime::getDefaultOutputFormat());
        $this->assertSame($expected, (String) $view->formatDateTime($dateTime));
    }

    public function testItShouldReturnAnEmptyStringIfIsInvalidValue()
    {
        $view = new View();
        $this->assertSame('', (String) $view->formatDateTime(null));
        $this->assertSame('', (String) $view->formatDateTime('invalid date time'));
    }

    public function testDefaultInputOutputFormat()
    {
        $inputFormat = 'Y.m.d H:i:s';
        $outputFormat = 'Y,m,d H:i:s';

        FormatDateTime::setDefaultInputFormat($inputFormat);
        FormatDateTime::setDefaultOutputFormat($outputFormat);

        $this->assertSame($inputFormat, FormatDateTime::getDefaultInputFormat());
        $this->assertSame($outputFormat, FormatDateTime::getDefaultOutputFormat());

        $view = new View();
        $helper = $view->formatDateTime('');
        $this->assertSame($inputFormat, $helper->getInputFormat());
        $this->assertSame($outputFormat, $helper->getOutputFormat());
    }
}