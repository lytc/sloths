<?php

namespace LazyTest\View\Helper;

use Lazy\Pagination\Paginator;
use Lazy\View\View;
use Lazy\View\Helper\Url;

class PaginateTest extends \PHPUnit_Framework_TestCase
{
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
                <li><a href="/foo?foo=bar&page=1">1</a></li>
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
                <li><a href="/foo?foo=bar&page=7">7</a></li>
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