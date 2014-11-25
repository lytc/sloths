<?php

namespace Sloths\Application\Service;

use Sloths\Db\Model\Collection;
use Sloths\Db\Sql\Select;
use Sloths\Pagination\Adapter\ArrayAdapter;
use Sloths\Pagination\Adapter\ModelCollection;

class Paginator extends AbstractService
{
    /**
     * @var
     */
    protected $paginator;

    /**
     * @var string
     */
    protected $pageParamName = 'page';

    /**
     * @param $name
     * @return $this
     */
    public function setPageParamName($name)
    {
        $this->pageParamName = $name;
        return $this;
    }

    /**
     * @param $data
     * @return Paginator
     */
    public function paginate($data)
    {
        if ($data instanceof Collection) {
            $data = new ModelCollection($data);
        } elseif ($data instanceof Select) {

        } elseif (is_array($data)) {
            $data = new ArrayAdapter($data);
        }

        $currentPage = (int) $this->getApplication()->getRequest()->getParams()->get($this->pageParamName);

        if (!$currentPage || $currentPage < 1) {
            $currentPage = 1;
        }

        return new \Sloths\Pagination\Paginator($data, $currentPage);
    }
}