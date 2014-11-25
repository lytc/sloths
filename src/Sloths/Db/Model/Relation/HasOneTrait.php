<?php

namespace Sloths\Db\Model\Relation;
use Sloths\Misc\ArrayUtils;

trait HasOneTrait
{
    /**
     * @var array
     */
    protected $hasOne = [];

    /**
     * @var array
     */
    protected $hasOneSchema;

    /**
     * @return HasOneSchema[]
     */
    public function getAllHasOneSchema()
    {
        if (null === $this->hasOneSchema) {
            $this->hasOneSchema = [];

            foreach ($this->hasOne as $name => $def) {
                if (is_numeric($name)) {
                    $name = $def;
                    $def = $def;
                }

                if (is_string($def)) {
                    $def = ['model' => $def];
                } elseif (!isset($def['model'])) {
                    $def['model'] = $name;
                }

                if ($namespaceName = $this->getNamespaceName()) {
                    if (0 !== strpos($def['model'], $namespaceName)) {
                        $def['model'] = $namespaceName . '\\' . $def['model'];
                    }
                }

                if (!isset($def['foreignKey'])) {
                    $def['foreignKey'] = $this->transformToForeignKeyColumnName($this->getTableName());
                }

                $this->hasOneSchema[$name] = new HasOneSchema($def['model'], $def['foreignKey']);
            }

        }

        return $this->hasOneSchema;
    }

    /**
     * @param $name
     * @return null|HasOneSchema
     */
    public function getHasOneSchema($name)
    {
        $all = $this->getAllHasOneSchema();

        return isset($all[$name])? $all[$name] : null;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasHasOne($name)
    {
        return !!$this->getHasOneSchema($name);
    }

    /**
     * @param $name
     * @return null|\Sloths\Db\Model\AbstractModel
     */
    public function getHasOne($name)
    {
        $schema = $this->getHasOneSchema($name);
        $foreignKeyColumn = $schema->getForeignKey();
        $model = $schema->getModel();
        $tableName = $model->getTableName();

        if ($parentCollection = $this->getParentCollection()) {
            $modelClassName = $schema->getModelClassName();
            $ids = $parentCollection->ids();

            $select = $model->table()->select()->where($tableName . '.' . $foreignKeyColumn . ' IN (' . implode(', ', $ids) . ')');
            $rows = $select->all();

            $pairs = ArrayUtils::column($rows, null, $foreignKeyColumn);
            foreach ($pairs as &$item) {
                $item = new $modelClassName($item);
            }

            foreach ($parentCollection as $model) {
                $id = $model->id();

                if (isset($pairs[$id])) {
                    $model->relationData[$name] = $pairs[$id];
                } else {
                    $model->relationData[$name] = null;
                }
            }

            $selfId = $this->id();
            return isset($pairs[$selfId])? $pairs[$selfId] : null;

        } else {
            return $model->first($tableName . '.' . $foreignKeyColumn . ' = ' . $this->id());
        }
    }
}