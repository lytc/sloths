<?php

namespace Lazy\Db\Model;

use Lazy\Db\Connection;
use Lazy\Db\Model\Relation\BelongsTo;
use Lazy\Db\Model\Relation\HasMany;
use Lazy\Db\Model\Relation\HasManyThrough;
use Lazy\Db\Model\Relation\HasOne;
use Lazy\Db\Sql\Delete;
use Lazy\Db\Sql\Insert;
use Lazy\Db\Sql\Update;
use Lazy\Util\Inflector;
use Lazy\Util\StringUtils;
use Lazy\Util\ArrayUtils;
use Lazy\Db\Sql\Select;

abstract class Model implements \JsonSerializable
{
    const INT          = 'int';
    const INTEGER      = self::INT;
    const TINYINT      = 'tinyint';
    const SMALLINT     = 'smallint';
    const MEDIUMINT    = 'mediumint';
    const BIGINT       = 'bigint';
    const DOUBLE       = 'double';
    const REAL         = self::DOUBLE;
    const FLOAT        = 'float';
    const DECIMAL      = 'decimal';
    const NUMERIC      = self::DECIMAL;
    const CHAR         = 'char';
    const VARCHAR      = 'varchar';
    const BINARY       = 'binary';
    const VARBINARY    = 'varbinary';
    const DATE         = 'date';
    const TIME         = 'time';
    const DATETIME     = 'datetime';
    const TIMESTAMP    = 'timestamp';
    const YEAR         = 'year';
    const TINYBLOB     = 'tinyblob';
    const BLOB         = 'blob';
    const MEDIUMBLOB   = 'mediumblob';
    const LONGBLOB     = 'longblob';
    const TINYTEXT     = 'tinytext';
    const TEXT         = 'text';
    const MEDIUMTEXT   = 'mediumtext';
    const LONGTEXT     = 'longtext';
    const ENUM         = 'enum';
    const SET          = 'set';
    const BOOLEAN      = 'boolean';

    /**
     * @var string
     */
    protected static $collectionClassName = 'Lazy\Db\Model\Collection';

    /**
     * @var string
     */
    protected static $primaryKey = 'id';
//    protected static $tableName;
//    protected static $columns = [];

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
     * @var Connection
     */
    protected static $connection;

    /**
     * @param Connection $connection
     */
    public static function setConnection(Connection $connection)
    {
        static::$connection = $connection;
    }

    /**
     * @return Connection
     */
    public static function getConnection()
    {
        return static::$connection;
    }

    /**
     * @return string
     */
    public static function getPrimaryKey()
    {
        return static::$primaryKey;
    }

    /**
     * @return string
     */
    public static function getTableName()
    {
        if (!isset(static::$tableName)) {
            static $tableName;
            if (!$tableName) {
                $tableName = StringUtils::getClassNameWithoutNamespaceName(get_called_class());
                $tableName = Inflector::tableize(Inflector::pluralize($tableName));
            }
            return $tableName;
        }

        return static::$tableName;
    }

    /**
     * @param bool [$withTablePrefix=true]
     * @return array
     */
    public static function getDefaultSelectColumns($withTablePrefix = true)
    {
        if (!isset(static::$defaultSelectColumns)) {
            static $defaultSelectColumns = [];

            if (!$defaultSelectColumns) {
                foreach (static::$columns as $columnName => $columnType) {
                    if (in_array($columnType, static::$defaultLazyLoadColumnTypes)) {
                        continue;
                    }
                    $defaultSelectColumns[] = $columnName;
                }
            }

            $columns = $defaultSelectColumns;
        } else {
            $columns = static::$defaultSelectColumns;
        }

        if ($withTablePrefix) {
            $tableName = static::getTableName();
            return array_map(function($column) use ($tableName) {
                return $tableName . '.' . $column;
            }, $columns);
        }
    }

    /**
     * @param mixed $columns
     * @return Select
     */
    public static function createSqlSelect($columns = null)
    {
        $select = new Select(static::getTableName());
        $columns || $columns = static::getDefaultSelectColumns();;
        $select->select($columns);

        return $select;
    }

    /**
     * @param int|string|array [$columnName]
     * @param mixed [$value]
     * @return static
     */
    public static function first($columnName = null, $value = null)
    {
        $select = static::createSqlSelect('*');
        $select->limit(1);

        if (is_numeric($columnName) && $columnName == (int) $columnName) {
            $where = static::$primaryKey . ' = ' . $columnName;
            $select->where($where);
        } elseif ($columnName) {
            call_user_func_array([$select, 'where'], func_get_args());
        }

        $row = static::getConnection()->select($select);

        if (!$row) {
            return;
        }

        $instance = new static($row);
        $instance->id = $row[static::$primaryKey];

        return $instance;
    }

    /**
     * @param string|array $where
     * @param mixed $params
     * @return Collection
     */
    public static function all($where = null, $params = null)
    {
        $select = static::createSqlSelect();

        if ($where) {
            if (is_array($where)) {
                if (ArrayUtils::hasOnlyInts($where)) {
                    $where = sprintf('%s IN(%s)', static::getTableName() . '.' . static::$primaryKey, implode(', ', $where));
                }
                $select->where($where);
            } else {
                call_user_func_array([$select, 'where'], func_get_args());
            }
        }

        $collection = new static::$collectionClassName($select, get_called_class());

        return $collection;
    }

    /**
     * @param array $data
     * @return static
     */
    public static function create(array $data = [])
    {
        return new static($data);
    }

    //// instance ////////////////////////
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $changedData = [];

    /**
     * @var
     */
    protected $id;

    /**
     * @var array
     */
    protected $relationData = [];

    /**
     * @var Collection
     */
    protected $fromCollection;

    /**
     * @param array $data
     * @param Collection $fromCollection
     */
    public function __construct(array $data = [], Collection $fromCollection = null)
    {
        if (isset($data[static::$primaryKey])) {
            $this->id = $data[static::$primaryKey];
            $data = ArrayUtils::pick($data, array_keys(static::$columns));
            $this->data = $data;
        } else {
            $this->changedData = $data;
        }

        $this->fromCollection = $fromCollection;
    }

    /**
     * @return bool
     */
    public function isExists()
    {
        return !!$this->id;
    }

    /**
     * @return int
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return mixed
     */
    protected function loadColumn($name)
    {
        $tableName = static::getTableName();
        $select = new Select($tableName);

        if ($this->fromCollection) {
            $select->select($tableName, [static::$primaryKey, $name]);

            $ids = $this->fromCollection->ids();
            $select->where(static::getTableName() . '.' . static::$primaryKey . ' IN(' . implode(', ', $ids) . ')');
            $rows = static::getConnection()->selectAll($select);
            foreach ($rows as $row) {
                $rows[$row[static::$primaryKey]] = $row[$name];
            }
            foreach ($this->fromCollection as $model) {
                $model->data[$name] = $rows[$model->id()];
            }
            return $this->data[$name];
        } else {
            $select->select($tableName, [$name]);
            $select->where(static::getTableName() . '.' . static::$primaryKey . ' = ' . $this->id);
            $result = static::getConnection()->selectColumn($select);

            $this->data[$name] = $result;
            return $result;
        }

    }

    /**
     * @param string $name
     * @return bool|Collection
     */
    protected function getHasManyRelation($name)
    {
        static $hasMany = [];

        if (!isset($hasMany[$name]) && isset(static::$hasMany)) {
            if (isset(static::$hasMany[$name])) {
                $hasMany[$name] = static::$hasMany[$name];
            } elseif (false !== array_search($name, static::$hasMany)) {
                $hasMany[$name] = $name;
            } else {
                $hasMany[$name] = false;
            }

            if ($definition = $hasMany[$name]) {
                if (is_string($definition)) {
                    $definition = ['model' => $definition];
                }

                isset($definition['model']) || $definition['model'] = $name;
                isset($definition['foreignKey']) || $definition['foreignKey'] = Inflector::singularize(static::getTableName()) . '_id';
                isset($definition['primaryKey']) || $definition['primaryKey'] = static::$primaryKey;
                class_exists($definition['model']) || $definition['model'] = StringUtils::getNamespace(get_called_class())
                    . '\\' . Inflector::classify(Inflector::singularize($definition['model']));

                $hasMany[$name] = function() use ($name, $definition) {
                    return new HasMany($name, $this, $definition['model'], $definition['primaryKey'], $definition['foreignKey'], $this->fromCollection);
                };
            }
        }

        if (isset($hasMany[$name]) && $hasMany[$name]) {
            return $this->relationData[$name] = call_user_func($hasMany[$name]->bindTo($this));
        }

        return false;
    }

    /**
     * @param string $name
     * @return bool|Model
     */
    protected function getBelongsToRelation($name)
    {
        static $belongsTo = [];

        if (!isset($belongsTo[$name]) && isset(static::$belongsTo)) {
            if (isset(static::$belongsTo[$name])) {
                $belongsTo[$name] = static::$belongsTo[$name];
            } else if (false !== array_search($name, static::$belongsTo)) {
                $belongsTo[$name] = $name;
            } else {
                $belongsTo[$name] = false;
            }

            if (!($definition = $belongsTo[$name])) {
                return false;
            }

            if (is_string($definition)) {
                $definition = ['model' => $definition];
            }

            isset($definition['model']) || $definition['model'] = $name;
            class_exists($definition['model']) || $definition['model'] = StringUtils::getNamespace(get_called_class())
                . '\\' . Inflector::classify(Inflector::singularize($definition['model']));

            isset($definition['foreignKey']) || $definition['foreignKey'] = Inflector::underscore(Inflector::singularize($name)) . '_id';
            isset($definition['primaryKey']) || $definition['primaryKey'] = $definition['model']::getPrimaryKey();
            $belongsTo[$name] = $definition;
        }

        if (isset($belongsTo[$name]) && ($definition = $belongsTo[$name])) {
            if ($this->fromCollection) {
                new BelongsTo($name, $this, $definition['model'], $definition['primaryKey'], $definition['foreignKey'], $this->fromCollection);
                return $this->relationData[$name];
            } else {
                return $this->relationData[$name] = $definition['model']::first($definition['model']::getTableName() . '.'. $definition['primaryKey'] . ' = ' . $this->{$definition['foreignKey']});
            }
        }

        return false;
    }

    /**
     * @param string $name
     * @return bool|Model
     */
    public function getHasOneRelation($name)
    {
        static $hasOne = [];

        if (!isset($hasOne[$name]) && isset(static::$hasOne)) {
            if (isset(static::$hasOne[$name])) {
                $hasOne[$name] = static::$hasOne[$name];
            } else if (false !== array_search($name, static::$hasOne)) {
                $hasOne[$name] = $name;
            } else {
                $hasOne[$name] = false;
            }

            if (!($definition = $hasOne[$name])) {
                return false;
            }

            if (is_string($definition)) {
                $definition = ['model' => $definition];
            }

            isset($definition['model']) || $definition['model'] = $name;
            class_exists($definition['model']) || $definition['model'] = StringUtils::getNamespace(get_called_class())
                . '\\' . Inflector::classify(Inflector::singularize($definition['model']));

            isset($definition['foreignKey']) || $definition['foreignKey'] = $definition['model']::getPrimaryKey();
            isset($definition['primaryKey']) || $definition['primaryKey'] = static::$primaryKey;
            $hasOne[$name] = $definition;
        }

        if (isset($hasOne[$name]) && ($definition = $hasOne[$name])) {
            if ($this->fromCollection) {
                new HasOne($name, $this, $definition['model'], $definition['primaryKey'], $definition['foreignKey'], $this->fromCollection);
                return $this->relationData[$name];
            } else {
                return $this->relationData[$name] = $definition['model']::first($definition['foreignKey'] . ' = ' . $this->{$definition['primaryKey']});
            }
        }

        return false;
    }

    /**
     * @param string $name
     * @return bool|Collection
     */
    protected function getHasManyThroughRelation($name)
    {
        static $hasManyThrough = [];

        if (!isset($hasManyThrough[$name]) && isset(static::$hasManyThrough)) {
            if (isset(static::$hasManyThrough[$name])) {
                $hasManyThrough[$name] = static::$hasManyThrough[$name];
            } elseif (false !== array_search($name, static::$hasManyThrough)) {
                $hasManyThrough[$name] = $name;
            } else {
                $hasManyThrough[$name] = false;
            }

            if ($definition = $hasManyThrough[$name]) {
                if (is_string($definition)) {
                    $definition = ['model' => $definition];
                }

                isset($definition['model']) || $definition['model'] = $name;
                class_exists($definition['model']) || $definition['model'] = StringUtils::getNamespace(get_called_class())
                    . '\\' . Inflector::classify(Inflector::singularize($definition['model']));

                if (!isset($definition['throughModel'])) {
                    $leftTable = static::getTableName();
                    $rightTable = static::getTableName();
                    $leftPart = Inflector::classify(Inflector::singularize($leftTable));
                    $rightPart = Inflector::classify(Inflector::singularize($rightTable));
                    $definition['throughModel'] = $leftPart > $rightPart? $leftPart . $rightPart : $rightPart . $leftPart;
                }

                class_exists($definition['throughModel']) || $definition['throughModel'] = StringUtils::getNamespace(get_called_class())
                    . '\\' . Inflector::classify(Inflector::singularize($definition['throughModel']));

                isset($definition['leftForeignKey']) || $definition['leftForeignKey'] = Inflector::singularize(static::getTableName()) . '_id';
                isset($definition['leftPrimaryKey']) || $definition['leftPrimaryKey'] = static::$primaryKey;
                isset($definition['rightForeignKey']) || $definition['rightForeignKey'] = Inflector::singularize($definition['model']::getTableName()) . '_id';
                isset($definition['rightPrimaryKey']) || $definition['rightPrimaryKey'] = $definition['model']::getPrimaryKey();

                $hasManyThrough[$name] = function() use ($name, $definition) {
                    return new HasManyThrough(
                        $name, $this,
                        $definition['model'],
                        $definition['throughModel'],
                        $definition['leftPrimaryKey'], $definition['leftForeignKey'],
                        $definition['rightPrimaryKey'], $definition['rightForeignKey'],
                        $this->fromCollection
                    );
                };
            }
        }


        if (isset($hasManyThrough[$name]) && $hasManyThrough[$name]) {
            return $this->relationData[$name] = call_user_func($hasManyThrough[$name]->bindTo($this));
        }

        return false;
    }

    /**
     * @param string $name
     * @return bool|Collection|Model
     */
    public function getRelation($name)
    {
        if (array_key_exists($name, $this->relationData)) {
            return $this->relationData[$name];
        }

        if (false !== ($relation = $this->getHasManyRelation($name))) {
            return $relation;
        }

        if (false !== ($relation = $this->getBelongsToRelation($name))) {
            return $relation;
        }

        if (false !== ($relation = $this->getHasOneRelation($name))) {
            return $relation;
        }

        if (false !== ($relation = $this->getHasManyThroughRelation($name))) {
            return $relation;
        }
    }

    /**
     * @param string $name
     * @param Collection|Model $value
     * @return $this
     */
    public function setRelation($name, $value)
    {
        $this->relationData[$name] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value)
    {
        $underscoreName = Inflector::underscore($name);

        if (array_key_exists($underscoreName, static::$columns)) {
            $this->changedData[$underscoreName] = $value;
        }
    }

    /**
     * @param $name
     * @return bool|Collection|Model|mixed
     * @throws \InvalidArgumentException
     */
    public function get($name)
    {
        $underscoreName = Inflector::underscore($name);

        if (array_key_exists($underscoreName, static::$columns)) {
            if (array_key_exists($underscoreName, $this->changedData)) {
                return $this->changedData[$underscoreName];
            }

            if (array_key_exists($underscoreName, $this->data)) {
                return $this->data[$underscoreName];
            }

            if ($this->id) {
                return $this->loadColumn($underscoreName);
            }
        }

        if (false !== ($relation = $this->getRelation($name))) {
            return $relation;
        }

        throw new \InvalidArgumentException(sprintf('Call to undefined property %s', $name));
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * @param string $name
     * @return bool|Collection|Model|mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @return array
     */
    public function getChanged()
    {
        return $this->changedData;
    }

    /**
     * @return bool
     */
    public function hasChanged()
    {
        return !!$this->getChanged();
    }

    /**
     * @return array
     */
    protected function getDataForSave()
    {
        $data = ArrayUtils::pick($this->changedData, array_keys(static::$columns));
        unset($data[static::$primaryKey]);
        return $data;
    }

    /**
     * @param array $data
     */
    protected function insert(array $data)
    {
        $insert = new Insert(static::getTableName());
        $insert->values($data);

        $id = static::getConnection()->insert($insert);
        $this->id = $id;
    }

    /**
     * @param array $data
     */
    protected function update(array $data)
    {
        $update = new Update(static::getTableName());
        $update->set($data)->where(static::$primaryKey . ' = ' . $this->id);
        static::getConnection()->update($update);
    }

    /**
     * @return $this
     */
    public function save()
    {
        $data = $this->getDataForSave();

        if (!$this->isExists()) {
            $this->insert($data);
        } else {
            if (!$data) {
                return $this;
            }
            $this->update($data);
        }

        $this->data = array_replace($this->data, $data);
        $this->changedData = [];

        return $this;
    }

    /**
     * @throws \LogicException
     */
    public function delete()
    {
        if (!$this->id) {
            throw new \LogicException('Cannot delete non existing model');
        }

        $sqlDelete = new Delete(static::getTableName());
        $sqlDelete->where(static::$primaryKey . ' = ' . $this->id);
        static::getConnection()->delete($sqlDelete);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = $this->data;

        if (isset(static::$hiddenColumns)) {
            static $columns;
            null !== $columns || $columns = array_combine(static::$hiddenColumns, static::$hiddenColumns);
            $data = array_diff_key($data, $columns);
        }

        return $data;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this, $options);
    }
}