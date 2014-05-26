<?php

namespace SlothsTest\View\Helper;

use Sloths\Pagination\Paginator;
use Sloths\View\Helper\Paginate;
use Sloths\View\View;
use Sloths\View\Helper\Url;

/**
 * @covers \Sloths\View\Helper\Paginate
 */
class PaginateTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAndSetDefaultTemplate()
    {
        $defaultTemplate = Paginate::getDefaultTemplate();

        Paginate::setDefaultTemplate('foo');
        $this->assertSame('foo', Paginate::getDefaultTemplate());

        Paginate::setDefaultTemplate($defaultTemplate);
    }

    public function testUrl()
    {
        $view = $this->getMock('Sloths\View\View', ['url']);
        $view->expects($this->at(0))->method('url')->with(['page' => 1]);
        $view->expects($this->at(1))->method('url')->with('foo', ['page' => 2]);
        $paginate = new Paginate($view);
        $paginate->url(1);

        $paginate->setUrl('foo');
        $paginate->url(2);
    }

    public function testGetAndSetTemplate()
    {
        $paginate = new Paginate(new View());
        $this->assertSame($paginate::getDefaultTemplate(), $paginate->getTemplate());

        $paginate->setTemplate('foo');
        $this->assertSame('foo', $paginate->getTemplate());
    }

    public function testPageParamName()
    {
        $paginate = new Paginate(new View());
        $this->assertSame('page', $paginate->getPageParamName());

        $paginate->setPageParamName('foo');
        $this->assertSame('foo', $paginate->getPageParamName());
    }

    public function testWithDefaultTemplate()
    {
        $minify = function($html) {
            $html = trim($html);
            $html = preg_replace('/\n/', '', $html);
            $html = preg_replace('/ +/', ' ', $html);
            return $html;
        };

        Url::setDefaultUrl('/foo?foo=bar');
        $view = new View();
        $rows = range(1, 31);
        $paginator = new Paginator($rows);
        $paginator->setItemsCountPerPage(2);

        $expected = '
            <ul class="pagination">
                <li class="disabled"><span>«</span></li>
                <li class="active"><a href="/foo?foo=bar&page=1">1</a></li>
                <li><a href="/foo?foo=bar&page=2">2</a></li>
                <li><a href="/foo?foo=bar&page=3">3</a></li>
                <li><a href="/foo?foo=bar&page=4">4</a></li>
                <li><a href="/foo?foo=bar&page=5">5</a></li>
                <li><a href="/foo?foo=bar&page=6">6</a></li>
                <li><a href="/foo?foo=bar&page=7">7</a></li>
                <li><a href="/foo?foo=bar&page=8">8</a></li>
                <li><a href="/foo?foo=bar&page=9">9</a></li>
                <li><a href="/foo?foo=bar&page=10">10</a></li>
                <li><a href="/foo?foo=bar&page=2">»</a></li>
            </ul>
        ';


        $paginate = $view->paginate($paginator);
        $result = $paginate->render();

        $this->assertSame($minify($expected), $minify($result));

        $expected = '
            <ul class="pagination">
                <li><a href="/foo?foo=bar&page=6">«</a></li>
                <li><a href="/foo?foo=bar&page=2">2</a></li>
                <li><a href="/foo?foo=bar&page=3">3</a></li>
                <li><a href="/foo?foo=bar&page=4">4</a></li>
                <li><a href="/foo?foo=bar&page=5">5</a></li>
                <li><a href="/foo?foo=bar&page=6">6</a></li>
                <li class="active"><a href="/foo?foo=bar&page=7">7</a></li>
                <li><a href="/foo?foo=bar&page=8">8</a></li>
                <li><a href="/foo?foo=bar&page=9">9</a></li>
                <li><a href="/foo?foo=bar&page=10">10</a></li>
                <li><a href="/foo?foo=bar&page=11">11</a></li>
                <li><a href="/foo?foo=bar&page=8">»</a></li>
            </ul>
        ';

        $paginator->setCurrentPage(7);
        $result = $paginate->render();

        $this->assertSame($minify($expected), $minify($result));
    }
}