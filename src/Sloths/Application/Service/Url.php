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
     * @param bool|true $full
     * @return string
     */
    public function base($full = true)
    {
        return $this->getApplication()->getBaseUrl($full);
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
     * @param bool $full
     * @return string
     */
    public function to($path = '', array $queryParams = [], $full = true)
    {
        $prefix = $this->base($full);

        if (!$path) {
            $path = $prefix;
        } else {
            if ('/' != $path[0] && !preg_match('/^https?:\/\//', $path)) {
                $path = rtrim($prefix, '/') . '/' . $path;
            }
        }

        return UrlUtils::appendParams($path, $queryParams);
    }

    /**
     * @param AbstractModel $model
     * @param array $queryParams
     * @param bool $full
     * @return string
     */
    public function view(AbstractModel $model, array $queryParams = [], $full = true)
    {
        return $this->to(Inflector::dasherize($model->getTableName()) . '/' . $model->id(), $queryParams, $full);
    }

    /**
     * @param AbstractModel $model
     * @param array $queryParams
     * @param bool $full
     * @return string
     */
    public function edit(AbstractModel $model, array $queryParams = [], $full = true)
    {
        return $this->to(Inflector::dasherize($model->getTableName()) . '/' . $model->id() . '/edit', $queryParams, $full);
    }

    /**
     * @param AbstractModel $model
     * @param array $queryParams
     * @param bool $full
     * @return string
     */
    public function update(AbstractModel $model, array $queryParams = [], $full = true)
    {
        return $this->to(Inflector::dasherize($model->getTableName()) . '/' . $model->id(), $queryParams, $full);
    }

    /**
     * @param AbstractModel $model
     * @param array $queryParams
     * @param bool $full
     * @return string
     */
    public function delete(AbstractModel $model, array $queryParams = [], $full = true)
    {
        return $this->to(Inflector::dasherize($model->getTableName()) . '/' . $model->id(), $queryParams, $full);
    }

    /**
     * @param Collection $collection
     * @param array $queryParams
     * @param bool $full
     * @return string
     */
    public function lists(Collection $collection, array $queryParams = [], $full = true)
    {
        $tableName = $collection->getModel()->getTableName();
        return $this->to(Inflector::dasherize($tableName), $queryParams, $full);
    }

    /**
     * @param Collection $collection
     * @param array $queryParams
     * @param bool $full
     * @return string
     */
    public function add(Collection $collection, array $queryParams = [], $full = true)
    {
        $tableName = $collection->getModel()->getTableName();
        return $this->to(Inflector::dasherize($tableName) . '/new', $queryParams, $full);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->current();
    }
}