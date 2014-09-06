<?php

namespace Sloths\Db\Model;

use Sloths\Db\Database;
use Sloths\Misc\ArrayUtils;

abstract class AbstractModel implements ModelInterface, \JsonSerializable
{
    const CREATED_TIME_COLUMN_NAME  = 'created_time';
    const MODIFIED_TIME_COLUMN_NAME = 'modified_time';

    /**
     * @var string
     */
    protected static $primaryKey = 'id';

    /**
     * @var string
     */
    protected static $tableName;

    /**
     * @var array
     */
    protected static $columns = [];

    /**
     * @var array
     */
    protected static $hiddenColumns = [];

    /**
     * @var string
     */
    protected static $collectionClassName = 'Sloths\Db\Model\Collection';

    /**
     * @var array
     */
    protected static $defaultLazyLoadColumnTypes = [
        self::TEXT,
        self::MEDIUMTEXT,
        self::LONGTEXT,
        self::BLOB,
        self::MEDIUMBLOB,
        self::LONGBLOB,
    ];

    /**
     * @var array
     */
    protected static $defaultSelectColumns;

    /**
     * @var bool
     */
    protected static $timestamps = true;

    /**
     * @var array
     */
    protected static $hasOne = [];

    /**
     * @var array
     */
    protected static $belongsTo = [];

    /**
     * @var array
     */
    protected static $hasMany = [];


    /**
     * @var Database
     */
    protected static $database;

    /**
     * @return Schema
     */
    public static function schema()
    {
        static $schema;
        if (!$schema) {
            $schema = new Schema(
                get_called_class(),
                static::$primaryKey,
                static::$tableName,
                static::$columns,
                static::$hiddenColumns,
                static::$defaultLazyLoadColumnTypes,
                static::$defaultSelectColumns,
                static::$hasOne,
                static::$belongsTo,
                static::$hasMany
            );
        }

        return $schema;
    }

    /**
     * @return string
     */
    public static function getPrimaryKey()
    {
        return static::schema()->getPrimaryKey();
    }

    /**
     * @return string
     */
    public static function getTableName()
    {
        return static::schema()->getTableName();
    }

    /**
     * @param Database $database
     */
    public static function setDatabase(Database $database)
    {
        static::$database = $database;
    }

    /**
     * @param bool $strict
     * @return Database
     * @throws \RuntimeException
     */
    public static function getDatabase($strict = true)
    {
        if (!static::$database && $strict) {
            throw new \RuntimeException('Database is required');
        }

        return static::$database;
    }

    /**
     * @param int|string|array $where
     * @param mixed $params
     * @return static
     */
    public static function first($where = null, $params = null)
    {
        $select = static::schema()->createSqlSelect('*');

        if ($where) {
            if (is_numeric($where)) {
                $select->where(static::wherePrimaryKeyColumn($where));
            } else {
                call_user_func_array([$select, 'where'], func_get_args());
            }
        }

        $select->limit(1);

        $stmt = static::getDatabase()->run($select);

        if ($stmt && $row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            return new static($row);
        }
    }

    /**
     * @param string|array $where
     * @param mixed $params
     * @return \Sloths\Db\Model\Collection
     */
    public static function all($where = null, $params = null)
    {
        $select = static::schema()->createSqlSelect(static::schema()->getDefaultSelectColumns());

        if ($where) {
            if (is_array($where) && ArrayUtils::hasOnlyInts($where)) {
                $select->where(static::wherePrimaryKeyColumn(' IN (' . implode(', ', $where ) . ')'));
            } else {
                call_user_func_array([$select, 'where'], func_get_args());
            }
        }

        $collectionClassName = static::$collectionClassName;
        return new $collectionClassName($select, get_called_class());
    }

    /**
     * @param array $data
     * @return static
     */
    public static function create($data = [])
    {
        $model = new static();
        $model->fromArray($data);

        return $model;
    }

    /**
     * @param string $column
     * @param string $condition
     * @return string
     */
    protected static function whereColumn($column, $condition)
    {
        return static::getTableName() . '.' . $column . $condition;
    }

    /**
     * @param string $condition
     * @return string
     */
    protected static function wherePrimaryKeyColumn($condition)
    {
        if (is_numeric($condition)) {
            $condition = ' = ' . $condition;
        }
        return static::whereColumn(static::getPrimaryKey(), $condition);
    }

    ///////////////////
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $changedData = [];

    /**
     * @var array
     */
    protected $relationsData = [];

    /**
     * @var Collection
     */
    protected $parentCollection;

    /**
     * @param array $data
     * @param Collection $parentCollection
     */
    public function __construct(array $data = [], Collection $parentCollection = null)
    {
        if ($data) {
            $this->fromArray($data);
        }

        $this->parentCollection = $parentCollection;
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return isset($this->data[static::getPrimaryKey()]);
    }

    /**
     * @return int
     */
    public function id()
    {
        return $this->exists()? $this->data[static::getPrimaryKey()] : null;
    }

    /**
     * @param array|\Traversable $data
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function fromArray($data)
    {
        if (!is_array($data) && !$data instanceof \Traversable) {
            throw new \InvalidArgumentException(sprintf(
                'Data must be an array or an instanceof \Traversable. %s given.', gettype($data)
            ));
        }

        foreach ($data as $k => $v) {
            $this->set($k, $v);
        }


        if (isset($data[static::getPrimaryKey()])) {
            $this->applyDataChange();
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function applyDataChange()
    {
        $this->data = array_replace($this->data, $this->changedData);
        $this->changedData = [];
        return $this;
    }

    /**
     * @return $this
     */
    public function reload()
    {
        if ($id = $this->id()) {
            $select = static::schema()->createSqlSelect('*');
            $select->where(static::wherePrimaryKeyColumn($id));

            if ($data = static::getDatabase()->run($select)->fetch(\PDO::FETCH_ASSOC)) {
                $this->fromArray($data);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getDataForSave()
    {
        $data = ArrayUtils::only($this->changedData, static::schema()->getColumns());
        unset($data[static::getPrimaryKey()]);

        return $data;
    }

    /**
     * @return $this
     */
    protected function doInsert()
    {
        $data = $this->getDataForSave();

        # timestamp?
        if ((static::$timestamps === true || static::$timestamps == static::CREATED_TIME_COLUMN_NAME)
            && !isset($data[static::CREATED_TIME_COLUMN_NAME])
            && static::schema()->hasColumn(static::CREATED_TIME_COLUMN_NAME)) {
            $now = $this->getDatabase()->now();
            $this->data[static::CREATED_TIME_COLUMN_NAME] = $data[static::CREATED_TIME_COLUMN_NAME] = $now;
        }

        $insert = static::schema()->createSqlInsert();
        $insert->values($data);
        $this->data[static::getPrimaryKey()] = (int) static::getDatabase()->run($insert);

        return true;
    }

    /**
     * @param bool $force
     * @return $this
     */
    protected function doUpdate($force = false)
    {
        $data = $this->getDataForSave();

        if (!$data && !$force) {
            return false;
        }

        if (!$force) {
            $data = array_diff($data, $this->data);
        } elseif (!$data) {
            $data = $this->data;
            unset($data[static::getPrimaryKey()]);
        }

        # timestamp?
        if ((static::$timestamps === true || static::$timestamps == static::MODIFIED_TIME_COLUMN_NAME)
            && !isset($data[static::MODIFIED_TIME_COLUMN_NAME])
            && static::schema()->hasColumn(static::MODIFIED_TIME_COLUMN_NAME)) {
            $now = $this->getDatabase()->now();
            $this->data[static::MODIFIED_TIME_COLUMN_NAME] = $data[static::MODIFIED_TIME_COLUMN_NAME] = $now;
        }

        if ($data) {
            $update = static::schema()->createSqlUpdate();
            $update->values($data)->where(static::wherePrimaryKeyColumn($this->id()));

            static::getDatabase()->run($update);
            return true;
        }

        return false;
    }

    /**
     * @param bool $force
     * @return $this
     */
    public function save($force = false)
    {
        if ($this->exists()) {
            $result = $this->doUpdate($force);
        } else {
            $result = $this->doInsert();
        }

        $this->applyDataChange();

        if ($result) {
            $this->touchParents();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function touch()
    {
        return $this->save(true);
    }

    /**
     * @return $this
     */
    protected function touchParents()
    {
        foreach (static::schema()->getAllRelation() as $name => $relationDef) {
            if ($relationDef['type'] = Schema::BELONGS_TO && !empty($relationDef['touchOnSave'])) {
                $parentModel = $this->getRelation($name);
                if ($parentModel) {
                    $parentModel->touch();
                }
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function delete()
    {
        if ($id = $this->id()) {
            $delete = static::schema()->createSqlDelete();
            $delete->where(static::wherePrimaryKeyColumn($id));
            $this->getDatabase()->run($delete);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param bool $cache
     * @return null|Collection|static
     */
    public function getRelation($name, $cache = false)
    {
        if ($cache && array_key_exists($name, $this->relationsData)) {
            return $this->relationsData[$name];
        }
        $relation = static::schema()->getRelation($name);

        if (!$relation) {
            return;
        }

        $type = $relation['type'];
        switch ($type) {
            case Schema::HAS_ONE:
                return $this->resolveHasOneRelation($name, $relation, $cache);

            case Schema::BELONGS_TO:
                return $this->resolveBelongsToRelation($name, $relation, $cache);

            case Schema::HAS_MANY:
                if ($id = $this->id()) {
                    if (isset($relation['through'])) {
                        return $this->resolveHasManyThroughRelation($name, $relation, $cache);
                    } else {
                        return $this->resolveHasManyRelation($name, $relation, $cache);
                    }
                }
        }
    }

    /**
     * @param string $name
     * @param array $def
     * @param bool $cache
     * @return AbstractModel|null
     */
    protected function resolveHasOneRelation($name, array $def, $cache)
    {
        $model      = $def['model'];
        $foreignKey = $def['foreignKey'];

        if ($cache && $parentCollection = $this->parentCollection) {
            $ids = $this->parentCollection->ids();
            $select = $model::schema()->createSqlSelect();
            $select->where($model::whereColumn($foreignKey, ' IN (' . implode(', ', $ids) . ')'));
            $stmt = $this->getDatabase()->run($select);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $rows = ArrayUtils::column($rows, null, $foreignKey);
            foreach ($rows as $id => &$row) {
                $row = new $model($row);
            }
            unset($row);

            foreach ($this->parentCollection as $m) {
                $id = $m->id();
                $relation = isset($rows[$id])? $rows[$id] : null;
                $m->relationsData[$name] = $relation;
            }

            $selfId = $this->id();
            return isset($rows[$selfId])? $rows[$selfId] : null;

        } else {
            $id = $this->id();
            $result = $id? $model::first($model::whereColumn($foreignKey, ' = ' . $id)) : null;
            $this->relationsData[$name] = $result;
        }

        return $result;
    }

    /**
     * @param string $name
     * @param array $def
     * @param bool $cache
     * @return AbstractModel|null
     */
    protected function resolveBelongsToRelation($name, array $def, $cache)
    {
        $model              = $def['model'];
        $foreignKey         = $def['foreignKey'];

        if ($cache && $parentCollection = $this->parentCollection) {
            $ids = $this->parentCollection->column($foreignKey);
            $ids = array_unique($ids);

            $select = $model::schema()->createSqlSelect();

            $relationPrimaryKey = $model::getPrimaryKey();
            $select->where($model::wherePrimaryKeyColumn(' IN (' . implode(', ', $ids) . ')'));
            $stmt = $this->getDatabase()->run($select);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $rows = ArrayUtils::column($rows, null, $relationPrimaryKey);
            foreach ($rows as $id => &$row) {
                $row = new $model($row);
            }
            unset($row);

            foreach ($this->parentCollection as $m) {
                $foreignKeyValue = $m->get($foreignKey);
                $relation = isset($rows[$foreignKeyValue])? $rows[$foreignKeyValue] : null;
                $m->relationsData[$name] = $relation;
            }

            $selfForeignKeyValue = $this->get($foreignKey);
            return isset($rows[$selfForeignKeyValue])? $rows[$selfForeignKeyValue] : null;

        } else {
            $foreignKeyValue    = $this->get($foreignKey);
            $result = $foreignKeyValue? $model::first($foreignKeyValue) : null;
            $this->relationsData[$name] = $result;
        }

        return $result;
    }

    /**
     * @param string $name
     * @param array $def
     * @param bool $cache
     * @return Collection
     */
    protected function resolveHasManyRelation($name, $def, $cache)
    {
        $model = $def['model'];
        $foreignKey = $def['foreignKey'];
        $collection = $model::all();
        $selfId = $this->id();

        if ($cache && $parentCollection = $this->parentCollection) {
            $foreignKeyValues = $parentCollection->ids();
            $foreignKeyValues = array_unique($foreignKeyValues);

            $collection->where($model::whereColumn($foreignKey, ' IN(' . implode(', ', $foreignKeyValues ) . ')'));

            $collection->addEventListener('load', function($e, &$rows) use ($foreignKey, $parentCollection, $name, $model, $selfId) {
                $groups = [];

                foreach ($rows as $row) {
                    isset($groups[$row[$foreignKey]]) || $groups[$row[$foreignKey]] = [];
                    $groups[$row[$foreignKey]][] = $row;
                }

                foreach ($parentCollection as $parentModel) {
                    $groupRows = isset($groups[$parentModel->id()])? $groups[$parentModel->id()] : [];
                    $parentModel->relationsData[$name] = new Collection($groupRows, $model);
                }

                $rows = isset($groups[$selfId])? $groups[$selfId] : [];
            });

        } else {
            $collection->where($model::whereColumn($foreignKey, ' = ' . $selfId));
        }

        $this->relationsData[$name] = $collection;

        return $collection;
    }

    /**
     * @param string $name
     * @param array $def
     * @param bool $cache
     * @return Collection
     */
    protected function resolveHasManyThroughRelation($name, $def, $cache)
    {
        $model              = $def['model'];
        $throughModel       = $def['through'];

//        foreach ($throughModel::schema()->)

        $allBelongsTo = $throughModel::schema()->getAllBelongsToRelation();

        foreach ($allBelongsTo as $belongsTo) {
            if ($belongsTo['model'] == get_called_class()) {
                $leftFK = $belongsTo['foreignKey'];
                continue;
            }

            if ($belongsTo['model'] == $model) {
                $rightFK = $belongsTo['foreignKey'];
                continue;
            }
        }

        $selfTableName      = $model::getTableName();
        $selfPK             = $model::getPrimaryKey();
        $throughTableName   = $throughModel::getTableName();
        $selfId             = $this->id();

        $collection = $model::all()->select($throughTableName . '.' . $leftFK);
        $collection->join($throughTableName, function($join)
            use ($selfTableName, $throughTableName, $selfPK, $leftFK, $rightFK, $collection, $name, $model, $selfId, $cache) {

            $selfTableName = $model::getTableName();
            $join->on($throughTableName. '.' . $rightFK . ' = ' . $selfTableName . '.' . $selfPK);

                if ($cache && $parentCollection = $this->parentCollection) {
                    $join->and($throughTableName. '.' . $leftFK . ' IN(' . implode(', ', $parentCollection->ids()) . ')');

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
                            $parentModel->relationsData[$name] = new Collection($groupRows, $model);
                        }

                        $rows = isset($groups[$selfId])? $groups[$selfId] : [];
                    });

                } else {
                    $join->and($throughTableName. '.' . $leftFK . ' = ' . $selfId);
                }

            });

        $this->relationsData[$name] = $collection;

        return $collection;
    }

    /**
     * @param string $column
     * @return string
     */
    protected function loadColumn($column)
    {
        if ($this->parentCollection) {
            $select = static::schema()->createSqlSelect([static::getPrimaryKey(), $column]);
            $select->where(static::wherePrimaryKeyColumn(' IN (' . implode(', ', $this->parentCollection->ids()) . ')'));

            $pairs = static::getDatabase()->run($select)->fetchAll(\PDO::FETCH_GROUP|\PDO::FETCH_COLUMN);

            foreach ($this->parentCollection as $model) {
                $model->data[$column] = $pairs[$model->id()];
            }

            return $pairs[$this->id()];

        } else {
            $select = static::schema()->createSqlSelect($column);
            $select->where(static::wherePrimaryKeyColumn($this->id()));

            $value = static::getDatabase()->run($select)->fetchColumn();
            $this->data[$column] = $value;

            return $value;
        }
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        # from column
        $columnName = Schema::transformPropertyNameToColumnName($name);

        if (array_key_exists($columnName, $this->changedData)) {
            return $this->changedData[$columnName];
        }

        if (array_key_exists($columnName, $this->data)) {
            return $this->data[$columnName];
        }

        # lazy loading
        if ($this->exists() && $this->schema()->hasColumn($columnName)) {
            return $this->loadColumn($columnName);
        }

        # from relations
        return $this->getRelation($name, true);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function set($name, $value)
    {
        $columnName = Schema::transformPropertyNameToColumnName($name);

        if (static::schema()->hasColumn($columnName)) {
            $this->changedData[$columnName] = $value;
        } else {

            $belongsToRelationDef = static::schema()->getRelation($name);

            if ($belongsToRelationDef &&
                $belongsToRelationDef['type'] = Schema::BELONGS_TO && $value instanceof $belongsToRelationDef['model']) {
                $this->relationsData[$name] = $value;
                $this->changedData[$belongsToRelationDef['foreignKey']] = $value->id();
            } else {
                $this->changedData[$name] = $value;
            }
        }

        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param string $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * @param string $method
     * @param $args
     * @return null|Collection|static
     * @throws \BadMethodCallException
     */
    public function __call($method, $args)
    {
        if (static::schema()->hasRelation($method)) {
            array_unshift($args, $method);
            return call_user_func_array([$this, 'getRelation'], $args);
        }

        throw new \BadMethodCallException(sprintf('Call to undefined method %s::%s()', get_called_class(), $method));
    }

    /**
     * @param $withRelations
     * @return array
     */
    public function toArray($withRelations = false)
    {
        $result = array_merge($this->data, $this->changedData);

        if ($withRelations) {
            $result = array_merge($result, $this->relationsData);
        }

        if (static::$hiddenColumns) {
            $result = ArrayUtils::except($result, static::$hiddenColumns);
        }

        return $result;
    }

    /**
     * @param string|array $columns
     * @return array
     */
    public function only($columns)
    {
        return ArrayUtils::only($this->toArray(), $columns);
    }

    /**
     * @param string|array $columns
     * @return array
     */
    public function except($columns)
    {
        return ArrayUtils::except($this->toArray(), $columns);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }


}