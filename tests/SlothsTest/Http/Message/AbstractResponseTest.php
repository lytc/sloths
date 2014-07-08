<?php

namespace SlothsTest\Http\Message;

use SlothsTest\TestCase;

/**
 * @covers Sloths\Http\Message\AbstractResponse
 */
class AbstractResponseTest extends TestCase
{
    public function testGetReasonPhrase()
    {
        $response = $this->getMock('Sloths\Http\Message\AbstractResponse', ['getStatusCode']);
        $response->expects($this->once())->method('getStatusCode')->willReturn(200);
        $this->assertSame('OK', $response->getReasonPhrase());
    }

    /**
     * @dataProvider dataProviderTestIs
     */
    public function testIs($code, $method)
    {
        $response = $this->getMockForAbstractClass('Sloths\Http\Message\AbstractResponse');
        $response->setStatusCode($code);

        $this->assertTrue($response->{$method}());
    }

    public function dataProviderTestIs()
    {
        return [
            [101, 'isInformational'],
            [201, 'isSuccessful'],
            [301, 'isRedirection'],
            [401, 'isClientError'],
            [501, 'isServerError'],
            [200, 'isOk'],
            [403, 'isForbidden'],
            [404, 'isNotFound']
        ];
    }
}