<?php

namespace Sloths\Db\Model\Relation;

use Sloths\Db\Model\Collection;
use Sloths\Db\Model\Model;

class BelongsTo
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
        $ids = $fromCollection->column($foreignKey);
        $ids = array_diff($ids, ['']);
        $ids = array_unique($ids);
        $collection = $modelClassName::all($ids);

        $map = [];

        foreach ($collection as $m) {
            $map[$m->id()] = $m;
        }

        foreach ($fromCollection as $m) {
            $foreignKeyValue = $m->$foreignKey;
            $relationModel = isset($map[$foreignKeyValue])? $map[$foreignKeyValue] : null;
            $m->setRelation($name, $relationModel);
        }
    }
}