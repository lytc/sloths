<?php

namespace SlothsTest\Application\Service;

use SlothsTest\TestCase;
use Sloths\Application\Service\DateTime;

/**
 * @covers Sloths\Application\Service\DateTime
 */
class DateTimeTest extends TestCase
{
    /**
     * @dataProvider dataProviderTestFormatDate
     */
    public function testFormatDate($input, $expected)
    {
        $dateTime = new DateTime();
        $this->assertSame($expected, $dateTime->formatDate($input));
    }

    public function dataProviderTestFormatDate()
    {
        return [
            ['', ''],
            ['2014-08-19', 'Aug 19, 2014']
        ];
    }

    /**
     * @dataProvider dataProviderTestFormatDateTime
     */
    public function testFormatDateTime($input, $expected)
    {
        $dateTime = new DateTime();
        $this->assertSame($expected, $dateTime->formatDateTime($input));
    }

    public function dataProviderTestFormatDateTime()
    {
        return [
            ['', ''],
            ['2014-08-19 21:33:02', 'Aug 19, 2014 09:33:02 PM']
        ];
    }

    public function testCustomFormat()
    {
        $dateTime = new DateTime();
        $dateTime->setDefaultFormatDate('d/m/Y');
        $dateTime->setDefaultFormatDateTime('d/m/Y H:i');

        $this->assertSame('19/08/2014', $dateTime->formatDate('2014-08-19'));
        $this->assertSame('19/08/2014 21:33', $dateTime->formatDateTime('2014-08-19 21:33:02'));
    }
}