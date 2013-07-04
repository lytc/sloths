<?php

namespace Lazy\Db;

use Doctrine\Common\Inflector\Inflector;
use Lazy\Db\Sql\Select;
use Lazy\Db\Sql\Insert;
use Lazy\Db\Sql\Update;
use Lazy\Db\Sql\Delete;

/**
 * Class AbstractModel
 * @package Lazy\Db
 */
abstract class AbstractModel
{
    /**
     *
     */
    const TYPE_INT          = 'int';
    /**
     *
     */
    const TYPE_INTEGER      = self::TYPE_INT;
    /**
     *
     */
    const TYPE_TINYINT      = 'tinyint';
    /**
     *
     */
    const TYPE_SMALLINT     = 'smallint';
    /**
     *
     */
    const TYPE_MEDIUMINT    = 'mediumint';
    /**
     *
     */
    const TYPE_BIGINT       = 'bigint';
    /**
     *
     */
    const TYPE_DOUBLE       = 'double';
    /**
     *
     */
    const TYPE_REAL         = self::TYPE_DOUBLE;
    /**
     *
     */
    const TYPE_FLOAT        = 'float';
    /**
     *
     */
    const TYPE_DECIMAL      = 'decimal';
    /**
     *
     */
    const TYPE_NUMERIC      = self::TYPE_DECIMAL;
    /**
     *
     */
    const TYPE_CHAR         = 'char';
    /**
     *
     */
    const TYPE_VARCHAR      = 'varchar';
    /**
     *
     */
    const TYPE_BINARY       = 'binary';
    /**
     *
     */
    const TYPE_VARBINARY    = 'varbinary';
    /**
     *
     */
    const TYPE_DATE         = 'date';
    /**
     *
     */
    const TYPE_TIME         = 'time';
    /**
     *
     */
    const TYPE_DATETIME     = 'datetime';
    /**
     *
     */
    const TYPE_TIMESTAMP    = 'timestamp';
    /**
     *
     */
    const TYPE_YEAR         = 'year';
    /**
     *
     */
    const TYPE_TINYBLOB     = 'tinyblob';
    /**
     *
     */
    const TYPE_BLOB         = 'blob';
    /**
     *
     */
    const TYPE_MEDIUMBLOB   = 'mediumblob';
    /**
     *
     */
    const TYPE_LONGBLOB     = 'longblob';
    /**
     *
     */
    const TYPE_TINYTEXT     = 'tinytext';
    /**
     *
     */
    const TYPE_TEXT         = 'text';
    /**
     *
     */
    const TYPE_MEDIUMTEXT   = 'mediumtext';
    /**
     *
     */
    const TYPE_LONGTEXT     = 'longtext';
    /**
     *
     */
    const TYPE_ENUM         = 'enum';
    /**
     *
     */
    const TYPE_SET          = 'set';
    /**
     *
     */
    const TYPE_BOOLEAN      = 'boolean';
    /**
     *
     */
    const TYPE_SERIALIZABLE = 'serializable';

    /**
     * @var array
     */
    protected static $defaultColumnSchemas = array(
        self::TYPE_INT => array(
            'type'      => self::TYPE_INT,
            'length'    => 11,
            'unsigned'  => true,
        ),
        self::TYPE_TINYINT => array(
            'type'      => self::TYPE_TINYINT,
            'length'    => 4,
        ),
        self::TYPE_SMALLINT => array(
            'type'      => self::TYPE_SMALLINT,
            'length'    => 6,
            'unsigned'  => true,
        ),
        self::TYPE_MEDIUMINT => array(
            'type'      => self::TYPE_MEDIUMINT,
            'length'    => 9,
            'unsigned'  => true,
        ),
        self::TYPE_BIGINT => array(
            'type'      => self::TYPE_BIGINT,
            'length'    => 21,
            'unsigned'  => true,
        ),
        self::TYPE_DOUBLE => array(
            'type'      => self::TYPE_DOUBLE,
            'length'    => 30,
        ),
        self::TYPE_FLOAT => array(
            'type'      => self::TYPE_FLOAT,
            'length'    => 20,
        ),
        self::TYPE_DECIMAL => array(
            'type'      => self::TYPE_DECIMAL,
            'length'    => 30,
        ),
        self::TYPE_CHAR => array(
            'type'      => self::TYPE_CHAR,
            'length'    => 255,
        ),
        self::TYPE_VARCHAR => array(
            'type'      => self::TYPE_VARCHAR,
            'length'    => 255,
        ),
        self::TYPE_BINARY => array(
            'type'      => self::TYPE_BINARY,
            'length'    => 255
        ),
        self::TYPE_VARBINARY => array(
            'type'      => self::TYPE_VARBINARY,
            'length'    => 255,
        ),
        self::TYPE_DATE => array(
            'type'      => self::TYPE_DATE,
        ),
        self::TYPE_TIME => array(
            'type'      => self::TYPE_TIME,
        ),
        self::TYPE_DATETIME => array(
            'type'      => self::TYPE_DATETIME,
        ),
        self::TYPE_TIMESTAMP => array(
            'type'      => self::TYPE_TIMESTAMP,
            'default'   => 'CURRENT_TIMESTAMP',
            'onUpdate'  => 'CURRENT_TIMESTAMP'
        ),
        self::TYPE_YEAR => array(
            'type'      => self::TYPE_YEAR,
        ),
        self::TYPE_TINYBLOB => array(
            'type'      => self::TYPE_TINYBLOB,
        ),
        self::TYPE_BLOB => array(
            'type'      => self::TYPE_BLOB,
        ),
        self::TYPE_MEDIUMBLOB => array(
            'type'      => self::TYPE_MEDIUMBLOB
        ),
        self::TYPE_LONGBLOB => array(
            'type'      => self::TYPE_LONGBLOB
        ),
        self::TYPE_TINYTEXT => array(
            'type'      => self::TYPE_TINYTEXT,
        ),
        self::TYPE_TEXT => array(
            'type'      => self::TYPE_TEXT
        ),
        self::TYPE_MEDIUMTEXT => array(
            'type'      => self::TYPE_MEDIUMTEXT,
        ),
        self::TYPE_LONGTEXT => array(
            'type'      => self::TYPE_LONGTEXT,
        ),
        self::TYPE_ENUM => array(
            'type'      => self::TYPE_ENUM
        ),
        self::TYPE_SET => array(
            'type'      => self::TYPE_SET
        ),
        self::TYPE_BOOLEAN => array(
            'type'          => self::TYPE_TINYINT,
            'customType'    => self::TYPE_BOOLEAN,
            'default'       => 0,
        ),
        self::TYPE_SERIALIZABLE => array(
            'type'          => self::TYPE_TEXT,
            'customType'    => self::TYPE_SERIALIZABLE
        ),
    );

    /**
     * @var array
     */
    protected static $lazyLoadColumnTypes = array(
        self::TYPE_TEXT, self::TYPE_MEDIUMTEXT, self::TYPE_LONGTEXT, self::TYPE_BLOB, self::TYPE_MEDIUMBLOB,
        self::TYPE_LONGBLOB, self::TYPE_SERIALIZABLE
    );

    /**
     * @var string
     */
    protected static $primaryKey = 'id';

    /**
     * @var bool
     */
    public static $disableTransformValue = false;

    /**
     * @var bool
     */
    public static $autoCreatedTime = true;

    /**
     * @var Connection
     */
    protected static $connection;

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
                $calledClass = get_called_class();
                $parts = explode('\\', $calledClass);
                $classNameWithoutNamespace = array_pop($parts);
                $tableName = Inflector::tableize($classNameWithoutNamespace);
                $tableName = Inflector::pluralize($tableName);
            }
            return $tableName;
        }

        return static::$tableName;
    }

    /**
     * @param string $columnName optional
     * @return array|null
     */
    public static function getColumnSchema($columnName = null)
    {
        static $initializeColumnSchema;
        if (!$initializeColumnSchema) {
            $columnSchemas = array();
            foreach (static::$columns as $colName => $schema) {
                if (is_string($schema)) {
                    $schema = array('type' => $schema);
                }
                $columnSchemas[$colName] = array_merge(self::$defaultColumnSchemas[$schema['type']], $schema);
            }

            # specific for primary key column
            if (!isset($columnSchemas[static::getPrimaryKey()]['auto'])) {
                $columnSchemas[static::getPrimaryKey()]['auto'] = true;
            }

            static::$columns = $columnSchemas;
            $initializeColumnSchema = true;
        }

        if (!$columnName) {
            return static::$columns;
        }

        return isset(static::$columns[$columnName])? static::$columns[$columnName] : null;
    }

    /**
     * @return array
     */
    public static function getLazyLoadColumns()
    {
        if (!isset(static::$lazyLoadColumns)) {
            static $lazyLoadColumns;
            if (!$lazyLoadColumns) {
                $lazyLoadColumns = array();
                foreach (static::getColumnSchema() as $columnName => $schema) {
                    if (in_array($schema['type'], self::$lazyLoadColumnTypes)) {
                        $lazyLoadColumns[] = $columnName;
                    }
                }
            }
            return $lazyLoadColumns;
        }

        return static::$lazyLoadColumns;
    }

    /**
     * @return array
     */
    public static function getDefaultSelectColumns()
    {
        if (!isset(static::$defaultSelectColumns)) {
            static $defaultSelectColumns;
            if (!$defaultSelectColumns) {
                $defaultSelectColumns = array_values(array_diff(array_keys(static::getColumnSchema()), static::getLazyLoadColumns()));
            }
            return $defaultSelectColumns;
        }

        return static::$defaultSelectColumns;
    }

    /**
     * @param string $associationName optional
     * @return array|null
     */
    public static function getOneToManySchema($associationName = null)
    {
        if (!isset(static::$oneToMany)) {
            return;
        }

        static $oneToMany = array();

        if (!$oneToMany) {
            $calledClass = get_called_class();
            $parts = explode('\\', $calledClass);
            $classNameWithoutNamespace = array_pop($parts);
            $namespace = implode('\\', $parts);

            if ($namespace) {
                $namespace .= '\\';
            }

            foreach (static::$oneToMany as $name => $schema) {
                if (is_numeric($name)) {
                    $name = $schema;
                    $schema = array();
                }

                if (!isset($schema['model'])) {
                    $schema['model'] = $namespace . Inflector::singularize($name);
                }

                if (!isset($schema['key'])) {
                    $schema['key'] = Inflector::tableize($classNameWithoutNamespace) . '_id';
                }

                $oneToMany[$name] = $schema;
            }
        }
        if (!$associationName) {
            return $oneToMany;
        }

        return isset($oneToMany[$associationName])? $oneToMany[$associationName] : null;
    }

    /**
     * @param string $associationName optional
     * @return array|null
     */
    public static function getManyToOneSchema($associationName = null)
    {
        if (!isset(static::$manyToOne)) {
            return;
        }

        static $manyToOne = array();

        if (!$manyToOne) {
            $calledClass = get_called_class();
            $parts = explode('\\', $calledClass);
            array_pop($parts);
            $namespace = implode('\\', $parts);

            if ($namespace) {
                $namespace .= '\\';
            }

            foreach (static::$manyToOne as $name => $schema) {
                if (is_numeric($name)) {
                    $name = $schema;
                    $schema = array();
                }

                if (!isset($schema['model'])) {
                    $schema['model'] = $namespace . $name;
                }

                if (!isset($schema['key'])) {
                    $schema['key'] = Inflector::tableize($name) . '_id';
                }

                $manyToOne[$name] = $schema;
            }
        }

        if (!$associationName) {
            return $manyToOne;
        }

        return isset($manyToOne[$associationName])? $manyToOne[$associationName] : null;
    }

    /**
     * @param string $associationName optional
     * @return array|null
     */
    public static function getManyToManySchema($associationName = null)
    {
        if (!isset(static::$manyToMany)) {
            return;
        }

        static $manyToMany = array();

        if (!$manyToMany) {
            $calledClass = get_called_class();
            $parts = explode('\\', $calledClass);
            $classNameWithoutNamespace = array_pop($parts);
            $namespace = implode('\\', $parts);

            if ($namespace) {
                $namespace .= '\\';
            }

            foreach (static::$manyToMany as $name => $schema) {
                if (is_numeric($name)) {
                    $name = $schema;
                    $schema = array();
                }

                if (is_string($schema)) {
                    $schema = array('through' => $schema);
                }

                if (!isset($schema['model'])) {
                    $schema['model'] = $namespace . Inflector::singularize($name);
                }

                if (!isset($schema['through'])) {
                    $schema['through'] = $namespace . $classNameWithoutNamespace . Inflector::singularize($name);
                } elseif ($namespace && false === strpos($schema['through'], '\\')) {
                    $schema['through'] = $namespace . $schema['through'];
                }

                if (!isset($schema['leftKey'])) {
                    $schema['leftKey'] = Inflector::tableize($classNameWithoutNamespace) . '_id';
                }

                if (!isset($schema['rightKey'])) {
                    $schema['rightKey'] = Inflector::tableize(Inflector::singularize($name)) . '_id';
                }

                $manyToMany[$name] = $schema;
            }
        }

        if (!$associationName) {
            return $manyToMany;
        }

        return isset($manyToMany[$associationName])? $manyToMany[$associationName] : null;
    }

    /**
     * @param string $name optional
     * @return array|null
     */
    public static function getAssociationSchema($name = null)
    {
        static $associationSchema;
        if (null === $associationSchema) {
            $oneToMany = static::getOneToManySchema()?: array();
            $manyToOne = static::getManyToOneSchema()?: array();
            $manyToMany = static::getManyToManySchema()?: array();
            $associationSchema = array_merge($oneToMany, $manyToOne, $manyToMany);
        }

        if (!$name) {
            return $associationSchema;
        }

        return isset($associationSchema[$name])? $associationSchema[$name] : null;
    }

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
        return static::$connection?: Connection::getDefaultInstance();
    }

    /**
     * @param string|array $columns
     * @return Select
     */
    public static function createSqlSelect($columns = null)
    {
        $tableName = static::getTableName();
        $select = new Select(static::getConnection());
        $select->from($tableName);

        if (!$columns) {
            $columns = static::getDefaultSelectColumns();
        }

        if (is_array($columns)) {
            $columns = array_map(function($column) use ($tableName) {
                if (preg_match('/^[\w_]+$/', $column)) {
                    $column = $tableName . '.' . $column;
                }
                return $column;
            }, $columns);
        }

        $select->column($columns);

        return $select;
    }

    /**
     * @return Insert
     */
    public static function createSqlInsert()
    {
        $insert = new Insert(static::getConnection());
        $insert->into(static::getTableName());

        return $insert;
    }

    /**
     * @return Delete
     */
    public static function createSqlDelete()
    {
        $delete = new Delete(static::getConnection());
        $delete->from(static::getTableName());

        return $delete;
    }

    /**
     * @return Update
     */
    public static function createSqlUpdate()
    {
        $update = new Update(static::getConnection());
        $update->from(static::getTableName());

        return $update;
    }

    /**
     * @param string|array $where optional
     * @param string|array $columns optional
     * @return static
     */
    public static function first($where = null, $columns = null)
    {
        if (is_numeric($where)) {
            $where = array(static::getPrimaryKey() => $where);
        }

        if (!$columns) {
            $columns = '*';
        }

        $select = static::createSqlSelect($columns);
        $select->limit(1);

        if ($where) {
            $select->where($where);
        }

        $row = $select->fetch(\PDO::FETCH_ASSOC);
        if (is_array($row)) {
            return new static($row);
        }

        return null;
    }

    /**
     * @param string|array $where optional
     * @return Collection
     */
    public static function all($where = null)
    {
        if (is_array($where)) {
            $isPrimaryKeyValueList = true;
            foreach ($where as $index => $item) {
                if (!is_numeric($index) || !is_numeric($item)) {
                    $isPrimaryKeyValueList = false;
                }
            }

            if ($isPrimaryKeyValueList) {
                $where = array(static::getPrimaryKey() . ' IN(?)' => $where);
            }
        }

        $select = static::createSqlSelect();

        if ($where) {
            $select->where($where);
        }

        return new Collection(get_called_class(), $select);
    }

    /**
     * @param array $data
     * @return static
     */
    public static function create(array $data = array())
    {
        return new static($data);
    }

    public static function remove($where)
    {
        if (is_numeric($where)) {
            $where = array($where);
        }

        if (is_array($where)) {
            $isPrimaryKeyValueList = true;
            foreach ($where as $index => $item) {
                if (!is_numeric($index) || !is_numeric($item)) {
                    $isPrimaryKeyValueList = false;
                }
            }

            if ($isPrimaryKeyValueList) {
                $where = array(static::getPrimaryKey() . ' IN(?)' => $where);
            }
        }

        $delete = static::createSqlDelete();
        $delete->where($where);
        return $delete->exec();
    }

    public static function insert(array $data)
    {
        $insert = static::createSqlInsert();
        $insert->value($data);
        return $insert->exec();
    }

    /////
    /**
     * @var \Lazy\Db\Sql\Select
     */
    protected $select;

    /**
     * @var array
     */
    protected $data = array();
    /**
     * @var array
     */

    protected $associationData = array();
    /**
     * @var array
     */

    protected $dirtyData = array();
    /**
     * @var Collection
     */

    protected $collection;
    /**
     * @var \Lazy\Db\Sql\Insert
     */

    protected $sqlInsert;

    /**
     * @var \Lazy\Db\Sql\Update
     */
    protected $sqlUpdate;

    /**
     * @var \Lazy\Db\Sql\Delete
     */
    protected $sqlDelete;

    /**
     * @param array $data
     * @param Collection $collection
     */
    public function __construct(array $data = array(), Collection $collection = null)
    {
        if (isset($data[static::getPrimaryKey()])) {
            $this->data = $data;
        } else {
            $this->fromArray($data);
        }

        $this->collection = $collection;
    }

    /**
     * @return string|null
     */
    public function id()
    {
        return $this->isExists()? $this->data[static::getPrimaryKey()] : null;
    }

    /**
     * @return bool
     */
    public function isExists()
    {
        return isset($this->data[static::getPrimaryKey()]);
    }

    /**
     * @return bool
     */
    public function isDirty()
    {
        return !!$this->dirtyData;
    }

    protected function transformValue($name, $value)
    {
        if (static::$disableTransformValue) {
            return $value;
        }

        $getter = 'get' . Inflector::classify($name);
        if (method_exists($this, $getter)) {
            return $this->{$getter}($value);
        }

        return $value;
    }

    /**
     * @param string $name
     * @return string|Collection
     * @throws Exception
     */
    public function __get($name)
    {
        if (!array_key_exists($name, static::$columns)) {
            $nameUnderscore = Inflector::tableize($name);
        } else {
            $nameUnderscore = $name;
        }

        if (array_key_exists($nameUnderscore, $this->dirtyData)) {
            return $this->transformValue($name, $this->dirtyData[$nameUnderscore]);
        }

        if (array_key_exists($nameUnderscore, $this->data)) {
            return $this->transformValue($name, $this->data[$nameUnderscore]);
        }

        if (array_key_exists($name, $this->associationData)) {
            return $this->associationData[$name];
        }

        # lazy load
        if (array_key_exists($nameUnderscore, static::getColumnSchema())) {
            $primaryKey = static::getPrimaryKey();

            if ($this->collection) {
                $ids = $this->collection->column($primaryKey);
                $lazyLoadSelect = static::createSqlSelect(array($primaryKey, $nameUnderscore));
                $lazyLoadSelect->where(array("$primaryKey IN(?)" => $ids));
                $rows = $lazyLoadSelect->fetchAll(\PDO::FETCH_ASSOC);
                $pairs = array();
                foreach ($rows as $row) {
                    $pairs[$row[$primaryKey]] = $row[$nameUnderscore];
                }
                foreach ($ids as $id) {
                    $model = $this->collection->get($id);
                    $model->set($nameUnderscore, $pairs[$id], false, false);
                }
                return $this->transformValue($name, $pairs[$this->id()]);
            } else {
                $lazyLoadSelect = static::createSqlSelect($nameUnderscore);
                $lazyLoadSelect->where(array($primaryKey => $this->id()));
                $value = $lazyLoadSelect->fetchColumn();
                $this->data[$nameUnderscore] = $value;
                return $this->transformValue($name, $value);
            }
        }

        # one to many
        $oneToManySchema = static::getOneToManySchema($name);
        if ($oneToManySchema) {
            if ($this->collection) {
                $thisId = $this->id();
                $ids = $this->collection->column(static::getPrimaryKey());

                $refModel = $oneToManySchema['model'];
                $refKey = $oneToManySchema['key'];
                $select = $refModel::createSqlSelect();
                $select->where(array("$refKey IN(?)" => $ids));

                $collectionInstancesMap = array();

                $callback = function($rows) use ($ids, $name, $refKey, $refModel, &$collectionInstancesMap) {
                    $pairs = array();
                    foreach ($rows as $row) {
                        if (!isset($pairs[$row[$refKey]])) {
                            $pairs[$row[$refKey]] = array();
                        }
                        $pairs[$row[$refKey]][] = $row;
                    }

                    foreach ($ids as $id) {
                        if (!isset($pairs[$id])) {
                            $pairs[$id] = array();
                        }
                        $collectionInstancesMap[$id]->setData($pairs[$id]);
                    }
                };

                foreach ($ids as $id) {
                    $model = $this->collection->get($id);
                    $collection = new Collection($refModel, $select, $callback);
                    $collectionInstancesMap[$id] = $collection;
                    $model->set($name, $collection, false, false);

                    if ($thisId == $id) {
                        $return = $collection;
                    }
                }

                return $return;
            } else {
                $collection = $oneToManySchema['model']::all(array($oneToManySchema['key'] => $this->id()));
                $this->associationData[$name] = $collection;
                return $collection;
            }
        }

        # many to one
        $manyToOneSchema = static::getManyToOneSchema($name);

        if ($manyToOneSchema) {
            if ($this->collection) {
                $refModel = $manyToOneSchema['model'];
                $primaryKey = $refModel::getPrimaryKey();
                $refKey = $manyToOneSchema['key'];

                $foreignKeyValue = $this->{$refKey};

                $pairs = $this->collection->pair($primaryKey, $refKey);
                $select = $refModel::createSqlSelect();
                $select->where(array("$primaryKey IN(?)" => array_unique(array_values($pairs))));

                $rows = $select->fetchAll(\PDO::FETCH_BOTH);
                $rowPairs = array();

                foreach ($rows as $row) {
                    $rowPairs[$row[$primaryKey]] = new $refModel($row);
                    if ($row[$primaryKey] == $foreignKeyValue) {
                        $return = $rowPairs[$row[$primaryKey]];
                    }
                }

                foreach ($pairs as $id => $foreignKeyValue) {
                    if ($foreignKeyValue) {
                        $this->collection->get($id)->set($name, $rowPairs[$foreignKeyValue]);
                    }
                }

                return $return;
            } else {
                $model = $manyToOneSchema['model']::first($this->{$manyToOneSchema['key']});
                $this->associationData[$name] = $model;
                return $model;
            }

        }

        # many to many
        $manyToManySchema = static::getManyToManySchema($name);
        if ($manyToManySchema) {
            $model      = $manyToManySchema['model'];
            $through    = $manyToManySchema['through'];
            $leftKey    = $manyToManySchema['leftKey'];
            $rightKey   = $manyToManySchema['rightKey'];
            $primaryKey = $model::getPrimaryKey();
            $tableName  = $model::getTableName();
            $throughTableName = $through::getTableName();

            if ($this->collection) {
                $thisId = $this->id();
                $ids = $this->collection->column(static::getPrimaryKey());
                $select = $model::createSqlSelect()
                    ->column("$throughTableName.$leftKey")
                    ->where(array("$throughTableName.$leftKey IN(?)" => $ids))
                    ->join($throughTableName, "$throughTableName.$rightKey = $tableName.$primaryKey");

                $collectionInstancesMap = array();
                $callback = function($rows) use ($ids, $leftKey, &$collectionInstancesMap) {
                    $pairs = array();
                    foreach ($rows as $row) {
                        if (!isset($pairs[$row[$leftKey]])) {
                            $pairs[$row[$leftKey]] = array();
                        }
                        $pairs[$row[$leftKey]][] = $row;
                    }

                    foreach ($ids as $id) {
                        if (!isset($pairs[$id])) {
                            $pairs[$id] = array();
                        }

                        $collectionInstancesMap[$id]->setData($pairs[$id]);
                    }
                };

                foreach ($ids as $id) {
                    $collection = new Collection($model, $select, $callback);
                    $collectionInstancesMap[$id] = $collection;
                    $this->collection->get($id)->set($name, $collection);
                    if ($thisId == $id) {
                        $return = $collection;
                    }
                }

                return $return;
            } else {
                $collection = $model::all(array("$throughTableName.$leftKey" => $this->id()));
                $collection->join($throughTableName, "$throughTableName.$rightKey = $tableName.$primaryKey");
                $this->associationData[$name] = $collection;
                return $collection;
            }
        }

        throw new Exception(sprintf('Call undefined property %s', $name));
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param bool $integrityCheck optional
     * @param bool $dirty optional
     * @return $this
     * @throws Exception
     */
    public function set($name, $value, $integrityCheck = true, $dirty = true)
    {
        if (!array_key_exists($name, static::$columns)) {
            $nameUnderscore = Inflector::tableize($name);
        } else {
            $nameUnderscore = $name;
        }

        if ($integrityCheck) {
            $columnSchemas = static::getColumnSchema();
            if (isset($columnSchemas[$nameUnderscore]['auto'])) {
                throw new Exception(sprintf('Can not set the auto column %s', $nameUnderscore));
            }

            $associationSchema = static::getAssociationSchema();
            if (!isset($columnSchemas[$nameUnderscore]) && !isset($associationSchema[$name])) {
                throw new Exception(sprintf('Trying to set undefined property %s', $name));
            }
        }

        if (static::getOneToManySchema($name)) {
            $this->associationData[$name] = $value;
            return $this;
        }

        if (static::getManyToOneSchema($name)) {
            $this->associationData[$name] = $value;
            return $this;
        }

        $setter = 'set' . Inflector::classify($name);
        if (method_exists($this, $setter)) {
            $value = $this->{$setter}($value);
        }

        if (array_key_exists($nameUnderscore, $this->data)) {
            if ($this->data[$nameUnderscore] == $value) {
                unset($this->dirtyData[$nameUnderscore]);
                return;
            }
        }

        if ($dirty) {
            $this->dirtyData[$nameUnderscore] = $value;
        } else {
            $this->data[$nameUnderscore] = $value;
        }
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }

    /**
     * @param array $data
     * @return $this
     */
    public function fromArray(array $data)
    {
        foreach ($data as $key => $value) {
            if (array_key_exists($key, static::getColumnSchema())) {
                $this->set($key, $value, false, true);
            }
        }

        return $this;
    }

    /**
     * @param bool $mergeDirtyData
     * @return array
     */
    public function toArray($mergeDirtyData = true)
    {
        if ($this->isExists()) {
            if ($mergeDirtyData) {
                return array_merge($this->data, $this->dirtyData);
            }
            return $this->data;
        } else {
            return $this->dirtyData;
        }
    }

    public function __clone()
    {
        # load all data
        $unloadColumns = array_keys(array_diff_key(static::getColumnSchema(), $this->data));
        if ($unloadColumns) {
            $select = static::createSqlSelect($unloadColumns);
            $select->where([self::getPrimaryKey() => $this->id()]);
            $this->data = array_merge($this->data, $select->fetch(\PDO::FETCH_ASSOC));
        }

        unset($this->data[static::getPrimaryKey()]);
        $this->dirtyData = $this->data;
    }

    // @codeCoverageIgnoreStart
    protected function beforeUpdate() {}
    protected function afterUpdate() {}
    protected function beforeInsert() {}
    protected function afterInsert() {}
    protected function beforeDelete() {}
    public function afterDelete() {}
    // @codeCoverageIgnoreEnd

    /**
     * @return Update
     */
    protected function getSqlUpdate()
    {
        if (!$this->sqlUpdate) {
            $this->sqlUpdate = new Update(static::getConnection());
            $this->sqlUpdate->from(static::getTableName());
        }

        $this->sqlUpdate->data($this->dirtyData);
        return $this->sqlUpdate;
    }

    /**
     * @return Insert
     */
    protected function getSqlInsert()
    {
        if (!$this->sqlInsert) {
            $this->sqlInsert = new Insert(static::getConnection());
            $this->sqlInsert->into(static::getTableName());
        }
        return $this->sqlInsert;
    }

    /**
     * @return Delete
     */
    protected function getSqlDelete()
    {
        if (!$this->sqlDelete) {
            $this->sqlDelete = new Delete(static::getConnection());
            $this->sqlDelete->from(static::getTableName());
        }

        return $this->sqlDelete;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function save()
    {
        if ($this->isExists()) {
            if (false === $this->beforeUpdate()) {
                return $this;
            }

            if (!$this->isDirty()) {
                return $this;
            }

            $this->getSqlUpdate()
                ->data($this->dirtyData)
                ->where(array(static::getPrimaryKey() => $this->id()))
                ->exec();

            $this->afterUpdate();
        } else {
            if (false === $this->beforeInsert()) {
                return $this;
            }

            if (!$this->isDirty()) {
                throw new Exception('Trying to save an empty row');
            }

            if (isset(static::$autoCreatedTime)) {
                $createdTime = static::getColumnSchema('created_time');
                if ($createdTime && $createdTime['type'] = self::TYPE_DATETIME) {
                    $this->createdTime = new Expr('NOW()');
                }
            }

            $this->getSqlInsert()
                ->value($this->dirtyData)
                ->exec();

            $this->data = $this->dirtyData;
            $this->dirtyData = array();
            $this->data[static::getPrimaryKey()] = static::getConnection()->lastInsertId();
            $this->afterInsert();
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->dirtyData = array();
        return $this;
    }

    /**
     * @return $this
     */
    public function refresh()
    {
        if ($this->isExists()) {
            if (!$this->select) {
                $this->select = static::createSqlSelect('*');
                $this->select->where(array(static::getPrimaryKey() => $this->id()));
            }

            $this->dirtyData = array();
            $this->data = $this->select->fetch(\PDO::FETCH_ASSOC);
        }

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function delete()
    {
        if (!$this->isExists()) {
            throw new Exception('Trying to delete a non existing row');
        }

        $this->beforeDelete();

        if (isset(static::$softDelete) && static::$softDelete) {
            $field = is_string(static::$softDelete)? static::$softDelete : 'deleted';
            $this->{$field} = 1;
            $this->save();
        } else {
            $delete = $this->getSqlDelete();
            $delete->where(array(static::getPrimaryKey() => $this->id()));
            $delete->exec();
        }
        $this->afterDelete();
        return $this;
    }
}