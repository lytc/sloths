<?php

namespace Sloths\Db\Model\Relation;

use Sloths\Misc\ArrayUtils;

trait BelongsToTrait
{
    /**
     * @var array
     */
    protected $belongsTo = [];

    /**
     * @var array
     */
    protected $belongsToSchema;

    /**
     * @return BelongsToSchema[]
     */
    public function getAllBelongsToSchema()
    {
        if (null === $this->belongsToSchema) {
            $this->belongsToSchema = [];

            foreach ($this->belongsTo as $name => $def) {
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
                    $def['foreignKey'] = $this->transformToForeignKeyColumnName($name);
                }

                $this->belongsToSchema[$name] = new BelongsToSchema($def['model'], $def['foreignKey'], isset($def['touchOnSave']));
            }

        }

        return $this->belongsToSchema;
    }

    /**
     * @param string $name
     * @return null|BelongsToSchema
     */
    public function getBelongsToSchema($name)
    {
        $all = $this->getAllBelongsToSchema();
        return isset($all[$name])? $all[$name] : null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasBelongsTo($name)
    {
        return !!$this->getBelongsToSchema($name);
    }

    /**
     * @param $name
     * @return null|\Sloths\Db\Model\AbstractModel
     */
    public function getBelongsTo($name)
    {
        $schema = $this->getBelongsToSchema($name);
        $foreignKeyColumn = $schema->getForeignKey();
        $model = $schema->getModel();
        $primaryKeyColumn = $model->getPrimaryKey();
        $tableName = $model->getTableName();

        if ($parentCollection = $this->getParentCollection()) {
            $modelClassName = $schema->getModelClassName();
            $foreignKeyIds = $parentCollection->column($foreignKeyColumn);
            $foreignKeyIds = array_unique($foreignKeyIds);
            $foreignKeyIds = array_diff($foreignKeyIds, [null]);

            $select = $model->table()->select()->where($tableName . '.' . $primaryKeyColumn . ' IN (' . implode(', ', $foreignKeyIds) . ')');
            $rows = $select->all();

            $pairs = ArrayUtils::column($rows, null, $primaryKeyColumn);
            foreach ($pairs as &$item) {
                $item = new $modelClassName($item);
            }

            foreach ($parentCollection as $model) {
                $foreignKeyValue = $model->get($foreignKeyColumn);

                if (isset($pairs[$foreignKeyValue])) {
                    $model->relationData[$name] = $pairs[$foreignKeyValue];
                } else {
                    $model->relationData[$name] = null;
                }
            }

            $selfForeignKeyValue = $this->get($foreignKeyColumn);
            return isset($pairs[$selfForeignKeyValue])? $pairs[$selfForeignKeyValue] : null;

        } else {
            return $model->first($tableName . '.' . $primaryKeyColumn . ' = ' . $this->get($foreignKeyColumn));
        }
    }
}