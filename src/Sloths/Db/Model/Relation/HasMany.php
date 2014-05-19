<?php

namespace Sloths\Db\Model\Relation;


use Sloths\Db\Model\Collection;
use Sloths\Db\Model\ArrayCollection;
use Sloths\Db\Model\Model;

class HasMany extends Collection
{
    /**
     * @param string $name
     * @param Model $model
     * @param string $modelClassName
     * @param string $primaryKey
     * @param string $foreignKey
     * @param Collection $fromCollection
     */
    public function __construct($name, Model $model, $modelClassName, $primaryKey, $foreignKey, Collection $fromCollection = null)
    {
        $collection = $modelClassName::all();
        $tableName = $modelClassName::getTableName();

        if ($fromCollection) {
            $collection->where("$tableName.$foreignKey IN(" . implode(', ', $fromCollection->ids()) . ')');
            $this->addListener('loaded', function(&$rows) use ($name, $fromCollection, $foreignKey, $modelClassName) {
                $pairs = [];
                foreach ($rows as $row) {
                    $foreignKeyValue = $row[$foreignKey];

                    if (!isset($pairs[$foreignKeyValue])) {
                        $pairs[$foreignKeyValue] = [];
                    }

                    $pairs[$foreignKeyValue][] = $row;
                }

                foreach ($fromCollection as $model) {
                    $id = $model->id();

                    if (!isset($pairs[$id])) {
                        $pairs[$id] = [];
                    }

                    $relation = new ArrayCollection($pairs[$id], $modelClassName);
                    $model->setRelation($name, $relation);
                }

                $rows = reset($pairs);
            });
        } else {
            $collection->where("$tableName.$foreignKey" . ' = ' . $model->id());
        }
        parent::__construct($collection->getSqlSelect(), $modelClassName);
    }
}