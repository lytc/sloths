<?php

namespace Sloths\Db\Model\Relation;

abstract class AbstractSchema
{
    /**
     * @var string
     */
    protected $modelClassName;

    /**
     * @var \Sloths\Db\Model\AbstractModel
     */
    protected $model;

    /**
     * @return string
     */
    public function getModelClassName()
    {
        return $this->modelClassName;
    }

    /**
     * @return \Sloths\Db\Model\AbstractModel
     */
    public function getModel()
    {
        if (!$this->model) {
            $modelClassName = $this->modelClassName;
            $this->model = new $modelClassName();
        }

        return $this->model;
    }
}