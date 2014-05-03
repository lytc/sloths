<?php

namespace Lazy\Db\Model\Relation;

use Lazy\Db\Model\Collection;
use Lazy\Db\Model\Model;
use Lazy\Db\Model\ArrayCollection;

class HasManyThrough extends Collection
{
    /**
     * @param string $name
     * @param Model $model
     * @param string $modelClassName
     * @param string $throughModelClassName
     * @param string $leftPrimaryKey
     * @param string $leftForeignKey
     * @param string $rightPrimaryKey
     * @param string $rightForeignKey
     * @param Collection $fromCollection
     */
    public function __construct(
        $name, Model $model,
        $modelClassName,
        $throughModelClassName,
        $leftPrimaryKey, $leftForeignKey,
        $rightPrimaryKey, $rightForeignKey,
        Collection $fromCollection = null
    )
    {
        $tableName = $modelClassName::getTableName();
        $throughTableName = $throughModelClassName::getTableName();

        $collection = $modelClassName::all();
        $collection->join($throughTableName, "$throughTableName.$rightForeignKey = $tableName.$rightPrimaryKey");

        if ($fromCollection) {
            $collection->select($throughTableName, [$leftForeignKey]);
            $collection->where("$throughTableName.$leftForeignKey" . ' IN(' . implode(', ', $fromCollection->ids()) . ')');
            $this->addListener('loaded', function(&$rows) use ($name, $fromCollection, $leftForeignKey, $modelClassName) {
                $pairs = [];
                foreach ($rows as $row) {
                    $foreignKeyValue = $row[$leftForeignKey];

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

                    $model->setRelation($name, new ArrayCollection($pairs[$id], $modelClassName));
                }

                $rows = reset($pairs);
            });
        } else {
            $collection->where("$throughTableName.$leftForeignKey" . ' = ' . $model->id());
        }
        parent::__construct($collection->getSqlSelect(), $modelClassName);
    }
}