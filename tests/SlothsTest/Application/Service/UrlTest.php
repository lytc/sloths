<?php

namespace SlothsTest\Application\Service;

use Sloths\Application\Service\Url;
use SlothsTest\Db\Model\Stub\Post;

class UrlTest extends TestCase
{
    public function testCurrent()
    {
        $request = $this->getMock('request', ['getUrl']);
        $request->expects($this->any())->method('getUrl')->with(true)->willReturn('foo');

        $application = $this->getMockApplication();
        $application->expects($this->any())->method('getRequest')->willReturn($request);

        $url = new Url();
        $url->setApplication($application);

        $this->assertSame('foo', $url->current());
        $this->assertSame('foo?bar=baz', $url->current(['bar' => 'baz']));
    }

    /**
     * @dataProvider dataProviderTestTo
     */
    public function testTo($applicationBaseUrl, $path, $params, $expected)
    {
        $application = $this->getMockApplication();
        $application->expects($this->any())->method('getBaseUrl')->willReturn($applicationBaseUrl);

        $url = new Url();
        $url->setApplication($application);

        $this->assertSame($expected, $url->to($path, $params));
    }

    public function dataProviderTestTo()
    {
        return [
            ['/', '', [], '/'],
            ['/', 'foo', [], '/foo'],
            ['/admin', '', [], '/admin'],
            ['/admin', 'foo', [], '/admin/foo'],
            ['/admin', 'foo', ['foo' => 'bar'], '/admin/foo?foo=bar'],
            ['/admin', '/foo', ['foo' => 'bar'], '/foo?foo=bar'],
            ['/admin', 'http://example.com/foo', ['foo' => 'bar'], 'http://example.com/foo?foo=bar'],
            ['/admin', '//example.com/foo', ['foo' => 'bar'], '//example.com/foo?foo=bar'],
        ];
    }

    public function testUrlToModelAndCollection()
    {
        $url = $this->getMock('Sloths\Application\Service\Url', ['to']);
        $url->expects($this->at(0))->method('to')->with('posts/1');
        $url->expects($this->at(1))->method('to')->with('posts/1/edit');
        $url->expects($this->at(2))->method('to')->with('posts/1');
        $url->expects($this->at(3))->method('to')->with('posts/1');
        $url->expects($this->at(4))->method('to')->with('posts');
        $url->expects($this->at(5))->method('to')->with('posts/new');

        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['getTableName' ,'id']);
        $model->expects($this->any())->method('getTableName')->willReturn('posts');
        $model->expects($this->any())->method('id')->willReturn('1');

        $collection = $this->getMockForAbstractClass('Sloths\Db\Model\Collection', [[], $model]);

        $url->view($model);
        $url->edit($model);
        $url->update($model);
        $url->delete($model);
        $url->lists($collection);
        $url->add($collection);
    }

    public function test__toString()
    {
        $url = $this->getMock('Sloths\Application\Service\Url', ['current']);
        $url->expects($this->once())->method('current')->willReturn('foo');

        $this->assertSame('foo', (string) $url);
    }

    public function test__invoke()
    {
        $url = $this->getMock('Sloths\Application\Service\Url', ['to']);
        $url->expects($this->once())->method('to')->with('foo', ['foo' => 'bar'])->willReturn('foo');

        $this->assertSame($url, $url->__invoke());
        $this->assertSame('foo', $url->__invoke('foo', ['foo' => 'bar']));
    }
}