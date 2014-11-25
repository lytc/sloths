<?php

namespace Sloths\Application\Service;

use Sloths\Db\Model\AbstractModel;
use Sloths\Db\Model\Collection;
use Sloths\Misc\Inflector;
use Sloths\Misc\UrlUtils;

class Url extends AbstractService
{
    /**
     * @return $this
     */
    public function __invoke()
    {
        if (func_num_args()) {
            return call_user_func_array([$this, 'to'], func_get_args());
        }

        return $this;
    }

    /**
     * @param array $overrideParamsQuery
     * @param bool $full
     * @return string
     */
    public function current($overrideParamsQuery = [], $full = true)
    {
        $url = $this->getApplication()->getRequest()->getUrl($full);

        if ($overrideParamsQuery) {
            $url = UrlUtils::appendParams($url, $overrideParamsQuery);
        }

        return $url;
    }

    /**
     * @param string $path
     * @param array $queryParams
     * @return string
     */
    public function to($path = '', array $queryParams = [])
    {
        $prefix = $this->getApplication()->getBaseUrl();

        if (!$path) {
            $path = $prefix;
        }

        if ('/' != $path[0] && !preg_match('/^https?:\/\//', $path)) {
            $path = $prefix . ($path? '/' . $path : '');
        }

        return UrlUtils::appendParams($path, $queryParams);
    }

    /**
     * @param AbstractModel $model
     * @param array $queryParams
     * @return string
     */
    public function view(AbstractModel $model, array $queryParams = [])
    {
        return $this->to(Inflector::dasherize($model->getTableName()) . '/' . $model->id(), $queryParams);
    }

    /**
     * @param AbstractModel $model
     * @param array $queryParams
     * @return string
     */
    public function edit(AbstractModel $model, array $queryParams = [])
    {
        return $this->to(Inflector::dasherize($model->getTableName()) . '/' . $model->id() . '/edit', $queryParams);
    }

    /**
     * @param AbstractModel $model
     * @param array $queryParams
     * @return string
     */
    public function update(AbstractModel $model, array $queryParams = [])
    {
        return $this->to(Inflector::dasherize($model->getTableName()) . '/' . $model->id(), $queryParams);
    }

    /**
     * @param AbstractModel $model
     * @param array $queryParams
     * @return string
     */
    public function delete(AbstractModel $model, array $queryParams = [])
    {
        return $this->to(Inflector::dasherize($model->getTableName()) . '/' . $model->id(), $queryParams);
    }

    /**
     * @param Collection $collection
     * @param array $queryParams
     * @return string
     */
    public function lists(Collection $collection, array $queryParams = [])
    {
        $tableName = $collection->getModel()->getTableName();
        return $this->to(Inflector::dasherize($tableName), $queryParams);
    }

    /**
     * @param Collection $collection
     * @param array $queryParams
     * @return string
     */
    public function add(Collection $collection, array $queryParams = [])
    {
        $tableName = $collection->getModel()->getTableName();
        return $this->to(Inflector::dasherize($tableName) . '/new', $queryParams);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->current();
    }
}