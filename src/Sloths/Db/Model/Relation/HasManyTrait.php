<?php

namespace Sloths\Db\Model\Relation;

trait HasManyTrait
{
    /**
     * @var array
     */
    protected $hasMany = [];

    /**
     * @var array
     */
    protected $hasManySchema;

    /**
     * @return HasManySchema[]|HasManyThroughSchema[]
     */
    public function getAllHasManySchema()
    {
        if (null === $this->hasManySchema) {
            $this->hasManySchema = [];

            foreach ($this->hasMany as $name => $def) {
                if (is_numeric($name)) {
                    $name = $def;
                    $def = $name;
                }

                if (is_string($def)) {
                    $def = ['model' => $def];
                } elseif (!isset($def['model'])) {
                    $def['model'] = $name;
                }

                if ($namespaceName = $this->getNamespaceName()) {
                    if (0 !== strpos($def['model'], $namespaceName)) {
                        $def['model'] = $namespaceName . '\\' . $this->transformTableNameToClassName($def['model']);
                    }
                }

                if (isset($def['through'])) {
                    if ($namespaceName && 0 !== strpos($def['through'], $namespaceName)) {
                        $def['through'] = $namespaceName . '\\' . $def['through'];
                    }
                    $schema = new HasManyThroughSchema(get_called_class(), $def['model'], $def['through']);
                } else {
                    if (!isset($def['foreignKey'])) {
                        $def['foreignKey'] = $this->transformToForeignKeyColumnName($this->getTableName());
                    }
                    $schema = new HasManySchema($def['model'], $def['foreignKey']);
                }

                $this->hasManySchema[$name] = $schema;
            }

        }

        return $this->hasManySchema;
    }

    /**
     * @param $name
     * @return null|HasManySchema|HasManyThroughSchema
     */
    public function getHasManySchema($name)
    {
        $all = $this->getAllHasManySchema();
        return isset($all[$name])? $all[$name] : null;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasHasMany($name)
    {
        return !!$this->getHasManySchema($name);
    }

    public function getHasMany($name, $cache = true)
    {
        if ($id = $this->id() && $schema = $this->getHasManySchema($name)) {
            if ($schema instanceof HasManyThroughSchema) {
                return $this->resolveHasManyThroughRelation($name, $schema, $cache);
            } else {
                return $this->resolveHasManyRelation($name, $schema, $cache);
            }
        }
    }

    /**
     * @param string $name
     * @param HasManySchema $schema
     * @param bool $cache
     * @return Collection
     */
    protected function resolveHasManyRelation($name, $schema, $cache)
    {
        $model          = $schema->getModel();
        $foreignKey     = $schema->getForeignKey();
        $collection     = $model->all();
        $selfId         = $this->id();
        $refTableName   = $model->getTableName();


        if ($cache && $parentCollection = $this->parentCollection) {
            $foreignKeyValues = $parentCollection->ids();
            $foreignKeyValues = array_unique($foreignKeyValues);

            $collection->where($refTableName . '.' . $foreignKey . ' IN (' . implode(', ', $foreignKeyValues ) . ')');
            $collection->addEventListener('load', function($e, &$rows) use ($foreignKey, $parentCollection, $name, $model, $selfId) {
                $groups = [];

                foreach ($rows as $row) {
                    isset($groups[$row[$foreignKey]]) || $groups[$row[$foreignKey]] = [];
                    $groups[$row[$foreignKey]][] = $row;
                }

                foreach ($parentCollection as $parentModel) {
                    $groupRows = isset($groups[$parentModel->id()])? $groups[$parentModel->id()] : [];
                    $collectionClassName = $model->collectionClassName;
                    $parentModel->relationData[$name] = new $collectionClassName($groupRows, $model);
                }

                $rows = isset($groups[$selfId])? $groups[$selfId] : [];
            });

        } else {
            $collection->where($refTableName . '.' . $foreignKey . ' = ' . $selfId);
        }

        $this->relationData[$name] = $collection;

        return $collection;
    }

    /**
     * @param string $name
     * @param HasManyThroughSchema $schema
     * @param bool $cache
     * @return Collection
     */
    protected function resolveHasManyThroughRelation($name, $schema, $cache)
    {
        $model              = $schema->getModel();
        $throughModel       = $schema->getThroughModel();
        $throughTableName   = $throughModel->getTableName();
        $leftFK             = $schema->getLeftForeignKey();
        $rightFK            = $schema->getRightForeignKey();

        $collection = $model->all()->select($throughTableName . '.' . $leftFK);

        $collection->join($throughTableName, function($join)
        use ($throughTableName, $leftFK, $rightFK, $collection, $name, $model, $throughModel, $cache) {
            $selfPK = $this->getPrimaryKey();
            $selfId = $this->id();

            $join->on($throughTableName. '.' . $rightFK . ' = ' . $model->getTableName() . '.' . $selfPK);

            if ($cache && $parentCollection = $this->parentCollection) {
                $join->and($throughTableName. '.' . $leftFK . ' IN (' . implode(', ', $parentCollection->ids()) . ')');

                $collection->addEventListener('load', function($e, &$rows)
                use ($collection, $leftFK, $parentCollection, $name, $model, $selfId) {

                    $groups = [];
                    foreach ($rows as $row) {
                        if ($leftPKValue = $row[$leftFK]) {
                            isset($groups[$leftPKValue]) || $groups[$leftPKValue] = [];
                        }

                        $groups[$leftPKValue][] = $row;

                    }

                    foreach ($parentCollection as $parentModel) {
                        $parentModelId = $parentModel->id();
                        $groupRows = isset($groups[$parentModelId])? $groups[$parentModelId] : [];
                        $collectionClassName = $model->collectionClassName;
                        $parentModel->relationData[$name] = new $collectionClassName($groupRows, $model);
                    }

                    $rows = isset($groups[$selfId])? $groups[$selfId] : [];
                });

            } else {
                $join->and($throughTableName. '.' . $leftFK . ' = ' . $selfId);
            }

        });

        $this->relationData[$name] = $collection;

        return $collection;
    }
}