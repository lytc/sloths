<?php

namespace SlothsTest\Application\Service;

use Sloths\Db\Model\Collection;

/**
 * @covers Sloths\Application\Service\Paginator
 */
class PaginatorTest extends TestCase
{
    protected function createApplicationMock()
    {
        $params = $this->getMock('Params', ['get']);
        $params->expects($this->once())->method('get')->with('_page')->willReturn('');

        $request = $this->getMock('Request', ['getParams']);
        $request->expects($this->once())->method('getParams')->willReturn($params);

        $application = $this->getMock('App', ['getRequest']);
        $application->expects($this->once())->method('getRequest')->willReturn($request);

        return $application;
    }

    public function testPaginate()
    {
        $paginator = $this->getMock('Sloths\Application\Service\Paginator', ['getApplication']);
        $paginator->expects($this->once())->method('getApplication')->willReturn($this->createApplicationMock());

        $paginator->setPageParamName('_page');

        $data = range(0, 200);
        $paginator = $paginator->paginate($data);
        $this->assertInstanceOf('Sloths\Pagination\Paginator', $paginator);
        $this->assertSame(1, $paginator->getCurrentPage());

        $adapter = $paginator->getAdapter();
        $this->assertInstanceOf('Sloths\Pagination\Adapter\ArrayAdapter', $adapter);
        $this->assertSame($data, $adapter->getData());
    }

    public function testPaginateModelCollection()
    {
        $paginator = $this->getMock('Sloths\Application\Service\Paginator', ['getApplication']);
        $paginator->expects($this->once())->method('getApplication')->willReturn($this->createApplicationMock());

        $paginator->setPageParamName('_page');

        $data = $this->getMock('Sloths\Db\Model\Collection', [], [], '', false);
        $paginator = $paginator->paginate($data);
        $this->assertInstanceOf('Sloths\Pagination\Paginator', $paginator);

        $adapter = $paginator->getAdapter();
        $this->assertInstanceOf('Sloths\Pagination\Adapter\ModelCollection', $adapter);
        $this->assertSame($data, $adapter->getModelCollection());
    }
}