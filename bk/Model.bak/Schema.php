<?php

namespace Sloths\Db\Model;

use Sloths\Db\Database;
use Sloths\Db\Sql\Select;
use Sloths\Db\Sql\Delete;
use Sloths\Db\Sql\Insert;
use Sloths\Db\Sql\Update;
use Sloths\Misc\StringUtils;
use Sloths\Misc\Inflector;

class Schema
{
    const HAS_ONE       = 1;
    const BELONGS_TO    = 2;
    const HAS_MANY      = 3;

    /**
     * @var string
     */
    protected $modelClassName;

    /**
     * @var string
     */
    protected $primaryKey;
    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var array
     */
    protected $columns;

    /**
     * @var array
     */
    protected $hiddenColumns;

    /**
     * @var array
     */
    protected $defaultLazyLoadColumnTypes;

    /**
     * @var array
     */
    protected $defaultSelectColumns;

    /**
     * @var array
     */
    protected $hasOne;

    /**
     * @var array
     */
    protected $belongsTo;

    /**
     * @var array
     */
    protected $hasMany;

    protected $hasOneRelations = null;
    protected $belongsToRelations = null;
    protected $hasManyRelations = null;

    /**
     * @var array
     */
    protected $columnNames;

    /**
     * @var
     */
    protected $namespaceName;

    /**
     * @var array
     */
    protected $relations;

    /**
     * @param string $modelClassName
     * @param string $primaryKey
     * @param string $tableName
     * @param array $columns
     * @param array $hiddenColumns
     * @param array $defaultLazyLoadColumnTypes
     * @param $defaultSelectColumns
     * @param array $hasOne
     * @param array $belongsTo
     * @param array $hasMany
     */
    public function __construct($modelClassName, $primaryKey, $tableName, array $columns, array $hiddenColumns,
                                array $defaultLazyLoadColumnTypes, $defaultSelectColumns,
                                array $hasOne, array $belongsTo, array $hasMany)
    {
        $this->modelClassName               = $modelClassName;
        $this->primaryKey                   = $primaryKey;
        $this->tableName                    = $tableName;
        $this->columns                      = $columns;
        $this->hiddenColumns                = $hiddenColumns;
        $this->defaultLazyLoadColumnTypes   = $defaultLazyLoadColumnTypes;
        $this->defaultSelectColumns         = $defaultSelectColumns;
        $this->hasOne                       = $hasOne;
        $this->belongsTo                    = $belongsTo;
        $this->hasMany                      = $hasMany;
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        if (!$this->tableName) {
            $this->tableName = static::transformClassNameToTableName($this->modelClassName);
        }

        return $this->tableName;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        if (!$this->columnNames) {
            $this->columnNames = array_keys($this->columns);
        }

        return $this->columnNames;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasColumn($name)
    {
        return isset($this->columns[$name]);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getColumnType($name)
    {
        return $this->columns[$name];
    }

    /**
     * @return array
     */
    public function getDefaultSelectColumns()
    {
        if ($this->defaultSelectColumns === null) {
            $this->defaultSelectColumns = [];
            foreach ($this->columns as $name => $type) {
                if (!in_array($type, $this->defaultLazyLoadColumnTypes)) {
                    $this->defaultSelectColumns[] = $name;
                }
            }
        }

        return $this->defaultSelectColumns;
    }

    /**
     * @param string|array $columns
     * @return Select
     */
    public function createSqlSelect($columns = null)
    {
        $columns || $columns = $this->getDefaultSelectColumns();

        $select = new Select();
        $select->table($this->getTableName())->select($columns);

        return $select;
    }

    /**
     * @return Insert
     */
    public function createSqlInsert()
    {
        $insert = new Insert();
        $insert->table($this->getTableName());

        return $insert;
    }

    /**
     * @return Update
     */
    public function createSqlUpdate()
    {
        $update = new Update();
        $update->table($this->getTableName());

        return $update;
    }

    /**
     * @return Delete
     */
    public function createSqlDelete()
    {
        $delete = new Delete();
        $delete->table($this->getTableName());

        return $delete;
    }

    protected function getNamespaceName()
    {
        if (null == $this->namespaceName) {
            $this->namespaceName = StringUtils::getNamespace($this->modelClassName);
        }

        return $this->namespaceName;
    }

    /**
     * @param $name
     * @param $def
     * @return array
     */
    protected function processHasOneRelationDefinition($name, $def)
    {
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
            $def['foreignKey'] = static::transformToForeignKeyColumnName($this->getTableName());
        }

        $def['type'] = self::HAS_ONE;

        return [$name, $def];
    }

    /**
     * @param $name
     * @param $def
     * @return array
     */
    protected function processBelongsToRelationDefinition($name, $def)
    {
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
            $def['foreignKey'] = static::transformToForeignKeyColumnName($name);
        }

        $def['type'] = self::HAS_ONE;

        return [$name, $def];
    }

    /**
     * @param $name
     * @param $def
     * @return array
     */
    protected function processHasManyRelationDefinition($name, $def)
    {
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
                $def['model'] = $namespaceName . '\\' . static::transformTableNameToClassName($def['model']);
            }
        }

        if (isset($def['through'])) {
            if ($namespaceName && 0 !== strpos($def['through'], $namespaceName)) {
                $def['through'] = $namespaceName . '\\' . $def['through'];
            }
        } elseif (!isset($def['foreignKey'])) {
            $def['foreignKey'] = static::transformToForeignKeyColumnName($this->getTableName());
        }

        $def['type'] = self::HAS_MANY;

        return [$name, $def];
    }

    /**
     * @return array|null
     */
    public function getAllHasOneRelation()
    {
        if (null === $this->hasOneRelations) {
            $hasOneRelations = [];
            foreach ($this->hasOne as $key => $def) {
                list($name, $def) = $this->processHasOneRelationDefinition($key, $def);
                $hasOneRelations[$name] = $def;
            }
            $this->hasOneRelations = $hasOneRelations;
        }

        return $this->hasOneRelations;
    }

    /**
     * @return array|null
     */
    public function getAllBelongsToRelation()
    {
        if (null === $this->belongsToRelations) {
            $belongsToRelations = [];
            foreach ($this->belongsTo as $key => $def) {
                list($name, $def) = $this->processBelongsToRelationDefinition($key, $def);
                $def['type'] = self::BELONGS_TO;
                $belongsToRelations[$name] = $def;
            }
            $this->belongsToRelations = $belongsToRelations;
        }

        return $this->belongsToRelations;
    }

    /**
     * @return array|null
     */
    public function getAllHasManyRelation()
    {
        if (null === $this->hasManyRelations) {
            $hasManyRelations = [];
            foreach ($this->hasMany as $key => $def) {
                list($name, $def) = $this->processHasManyRelationDefinition($key, $def);
                $hasManyRelations[$name] = $def;
            }
            $this->hasManyRelations = $hasManyRelations;
        }

        return $this->hasManyRelations;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getHasOneRelation($name)
    {
        $relations = $this->getAllHasOneRelation();
        return isset($relations[$name])? $relations[$name] : null;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getBelongsToRelation($name)
    {
        $relations = $this->getAllBelongsToRelation();
        return isset($relations[$name])? $relations[$name] : null;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getHasManyRelation($name)
    {
        $relations = $this->getAllHasManyRelation();
        return isset($relations[$name])? $relations[$name] : null;
    }

    /**
     * @return array
     */
    public function getAllRelation()
    {
        # initialize relations
        if (null === $this->relations) {
            $this->relations = array_merge(
                $this->getAllHasOneRelation(),
                $this->getAllBelongsToRelation(),
                $this->getAllHasManyRelation()
            );
        }

        return $this->relations;
    }

    /**
     * @param string $name
     * @return array|null
     */
    public function getRelation($name)
    {
        return $this->getHasOneRelation($name)?:
            ($this->getBelongsToRelation($name)?: $this->getHasManyRelation($name));
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasRelation($name)
    {
        return $this->hasHasOneRelation($name)?:
            ($this->hasBelongsToRelation($name)?: $this->hasHasManyRelation($name));
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasHasOneRelation($name)
    {
        return !!$this->getHasOneRelation($name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasBelongsToRelation($name)
    {
        return !!$this->getBelongsToRelation($name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasHasManyRelation($name)
    {
        return !!$this->getHasManyRelation($name);
    }

    /**
     * @param string $tableName
     * @return string
     */
    public static function transformTableNameToClassName($tableName)
    {
        return Inflector::singularize(Inflector::classify($tableName));
    }

    /**
     * @param string $className
     * @return string
     */
    public static function transformClassNameToTableName($className)
    {
        $tableName = StringUtils::getClassBaseName($className);
        $tableName = Inflector::underscore($tableName);
        $tableName = Inflector::pluralize($tableName);

        return $tableName;
    }

    /**
     * @param string $columnName
     * @return string
     */
    public static function transformColumnNameToPropertyName($columnName)
    {
        return Inflector::camelize($columnName);
    }

    /**
     * @param string $propertyName
     * @return string
     */
    public static function transformPropertyNameToColumnName($propertyName)
    {
        return Inflector::underscore($propertyName);
    }

    /**
     * @param string $name
     * @return string
     */
    public static function transformToForeignKeyColumnName($name)
    {
        return Inflector::underscore(Inflector::singularize($name)) . '_id';
    }
}