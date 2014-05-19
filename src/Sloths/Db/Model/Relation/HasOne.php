<?php

namespace Sloths\Db\Model\Relation;

use Sloths\Db\Model\Collection;
use Sloths\Db\Model\Model;

class HasOne
{
    /**
     * @param string $name
     * @param Model $model
     * @param string $modelClassName
     * @param string $primaryKey
     * @param string $foreignKey
     * @param Collection $fromCollection
     */
    public function __construct($name, Model $model, $modelClassName, $primaryKey, $foreignKey, Collection $fromCollection)
    {
        $ids = $fromCollection->column($primaryKey);
        $ids = array_diff($ids, ['']);
        $ids = array_unique($ids);
        $collection = $modelClassName::all($ids);

        $map = [];

        foreach ($collection as $m) {
            $map[$m->id()] = $m;
        }

        foreach ($fromCollection as $m) {
            $id = $m->id();
            $relationModel = isset($map[$id])? $map[$id] : null;
            $m->setRelation($name, $relationModel);
        }
    }
}