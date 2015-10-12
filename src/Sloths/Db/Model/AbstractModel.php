<?php

namespace Sloths\Db\Model;

use Sloths\Db\ConnectionManager;
use Sloths\Db\Model\Relation\BelongsToTrait;
use Sloths\Db\Model\Relation\HasManyThroughSchema;
use Sloths\Db\Model\Relation\HasManyTrait;
use Sloths\Db\Model\Relation\HasOneTrait;
use Sloths\Misc\ArrayUtils;
use Sloths\Misc\StringUtils;
use Sloths\Observer\ObserverTrait;

/**
 * @method static $this first()
 * @method static $this all()
 */
class AbstractModel implements \JsonSerializable, \Serializable
{
    use TransformNameTrait;
    use BelongsToTrait;
    use HasOneTrait;
    use HasManyTrait;
    use ObserverTrait;

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

    const CREATED_TIME_COLUMN_NAME  = 'created_time';
    const MODIFIED_TIME_COLUMN_NAME = 'modified_time';

    protected static $globalEventListeners = [];
    public static function setGlobalEventListeners(array $listeners)
    {
        static::$globalEventListeners = $listeners;
    }

    /**
     * @var string
     */
    protected $primaryKey = 'id';

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
    protected $hiddenColumns = [];

    /**
     * @var array
     */
    protected $lazyLoadColumnTypes = [
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
    protected $defaultSelectColumns;

    /**
     * @var string
     */
    protected $collectionClassName = 'Sloths\Db\Model\Collection';

    /**
     * @var bool
     */
    protected $timestamps = true;

    /**
     * @var ConnectionManager
     */
    protected static $defaultConnectionManager;

    /**
     * @var ConnectionManager
     */
    private $connectionManager;

    /**
     * @var Table
     */
    private $table;

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
    protected $relationData = [];

    /**
     * @var array
     */
    protected $mixedData = [];

    /**
     * @var Collection
     */
    private $parentCollection;

    /**
     * @param array|\Traversable $data
     * @param Collection $parentCollection
     */
    public function __construct($data = [], Collection $parentCollection = null)
    {
        $this->addEventListeners(static::$globalEventListeners);
        $this->setData($data);
        $this->parentCollection = $parentCollection;
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
            $this->tableName = $this->transformClassNameToTableName(get_called_class());
        }

        return $this->tableName;
    }

    /**
     * @return array
     */
    public function getColumnsSchema()
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getLazyLoadColumnTypes()
    {
        return $this->lazyLoadColumnTypes;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return array_keys($this->columns);
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
     * @return array
     */
    public function getDefaultSelectColumns()
    {
        if (null === $this->defaultSelectColumns) {
            $this->defaultSelectColumns = [];
            $lazyLoadColumnTypes = $this->getLazyLoadColumnTypes();

            foreach ($this->getColumnsSchema() as $name => $type) {
                if (!in_array($type, $lazyLoadColumnTypes)) {
                    $this->defaultSelectColumns[] = $name;
                }
            }
        }

        return $this->defaultSelectColumns;
    }

    /**
     * @return array
     */
    public function getHiddenColumns()
    {
        return $this->hiddenColumns;
    }

    /**
     * @return string
     */
    public function getNamespaceName()
    {
        return StringUtils::getNamespace(get_called_class());
    }

    /**
     * @param ConnectionManager $connectionManager
     */
    public static function setDefaultConnectionManager(ConnectionManager $connectionManager)
    {
        static::$defaultConnectionManager = $connectionManager;
    }

    /**
     * @param ConnectionManager $connectionManager
     * @return $this
     */
    public function setConnectionManager(ConnectionManager $connectionManager)
    {
        $this->connectionManager = $connectionManager;

        if (!static::$defaultConnectionManager) {
            static::$defaultConnectionManager = $connectionManager;
        }

        return $this;
    }

    /**
     * @param bool $strict
     * @return ConnectionManager
     * @throws \RuntimeException
     */
    public function getConnectionManager($strict = true)
    {
        if (!$this->connectionManager) {
            $this->connectionManager = static::$defaultConnectionManager;
        }

        if (!$this->connectionManager && $strict) {
            throw new \RuntimeException('A database connection manager is required');
        }

        return $this->connectionManager;
    }

    /**
     * @param array|\Traversable $data
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setData($data)
    {
        if (!is_array($data) && !$data instanceof \Traversable) {
            throw new \InvalidArgumentException(sprintf(
                'Data must be an array or an instanceof \Traversable. %s given.', gettype($data)
            ));
        }

        foreach ($data as $k => $v) {
            $this->set($k, $v);
        }


        if (isset($data[$this->getPrimaryKey()])) {
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
            $select = $this->table()->select('*');
            $select->where($this->getPrimaryKey() . ' = ' . $id);

            $row = $select->first();
            $this->setData($row);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getParentCollection()
    {
        return $this->parentCollection;
    }

    /**
     * @param $name
     * @return mixed
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function loadColumn($name)
    {
        if (!$this->hasColumn($name)) {
            throw new \InvalidArgumentException('Model has no column ' . $name);
        }

        if (!$this->exists()) {
            throw new \LogicException('Cannot load column from non existing record');
        }

        if ($parentCollection = $this->getParentCollection()) {
            $primaryKeyColumn = $this->getPrimaryKey();

            $ids = $parentCollection->ids();
            $select = $this->table()->select([$primaryKeyColumn, $name]);
            $select->where($primaryKeyColumn . ' IN (' . implode(', ', $ids) . ')');
            $rows = $select->all();
            $pairs = ArrayUtils::column($rows, $name, $primaryKeyColumn);

            foreach ($parentCollection as $model) {
                $id = $model->id();

                if (isset($pairs[$id])) {
                    $model->data[$name] = $pairs[$id];
                } else {
                    $model->data[$name] = null;
                }
            }

            return $pairs[$this->id()];

        } else {
            $select = $this->table()->select($name);
            $select->where($this->getPrimaryKey() . ' = ' . $this->id());
            $row = $select->first();
            $value = $row[$name];

            $this->data[$name] = $value;
            return $value;
        }
    }

    /**
     * @return array
     */
    public function getOriginalData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getChangedData()
    {
        return $this->changedData;
    }

    /**
     * @return array
     */
    public function getMixedData()
    {
        return $this->mixedData;
    }

    /**
     * @return array
     */
    public function getDataForSave()
    {
        $data = array_diff_assoc($this->getChangedData(), $this->getOriginalData());
        $data = ArrayUtils::only($data, $this->getColumns());
        unset($data[$this->getPrimaryKey()]);

        return $data;
    }

    /**
     * @return $this
     */
    protected function doInsert()
    {
        $this->triggerEventListener('insert', [$this]);
        $data = $this->getDataForSave();

        # timestamp?
        if (($this->timestamps === true || $this->timestamps == static::CREATED_TIME_COLUMN_NAME)
            && !isset($data[static::CREATED_TIME_COLUMN_NAME])
            && $this->hasColumn(static::CREATED_TIME_COLUMN_NAME)) {
            $now = $this->getConnectionManager()->now();
            $this->data[static::CREATED_TIME_COLUMN_NAME] = $data[static::CREATED_TIME_COLUMN_NAME] = $now;
        }

        $this->table()->insert($data)->run();
        $id = (int) $this->getConnectionManager()->getWriteConnection()->getLastInsertId();
        $this->data[$this->getPrimaryKey()] = $id;

        $this->triggerEventListener('inserted', [$this]);

        return true;
    }

    /**
     * @param bool $force
     * @return $this
     */
    protected function doUpdate($force = false)
    {
        $this->triggerEventListener('update', [$this]);

        $data = $this->getDataForSave();

        if (!$data && !$force) {
            return false;
        }

        $id = $this->id();

        # timestamp?
        if (($this->timestamps === true || $this->timestamps == static::MODIFIED_TIME_COLUMN_NAME)
            && !isset($data[static::MODIFIED_TIME_COLUMN_NAME])
            && $this->hasColumn(static::MODIFIED_TIME_COLUMN_NAME)) {
            $now = $this->getConnectionManager()->now();
            $this->data[static::MODIFIED_TIME_COLUMN_NAME] = $data[static::MODIFIED_TIME_COLUMN_NAME] = $now;
        }

        if (!$data) {
            $data = [$this->getPrimaryKey() => $id];
        }

        $this->table()->update($data)->where($this->getPrimaryKey() . ' = ' . $id)->run();

        $this->triggerEventListener('updated', [$this]);
        return true;
    }

    /**
     * @param bool $force
     * @return $this
     */
    public function save($force = false)
    {
        $this->triggerEventListener('save', [$this]);

        if ($this->exists()) {
            $result = $this->doUpdate($force);
        } else {
            $result = $this->doInsert();
        }

        if ($result) {
            $this->applyDataChange();
            $this->touchParents();
        }

        $this->triggerEventListener('saved', [$this]);
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
        foreach ($this->getAllBelongsToSchema() as $name => $schema) {
            if ($schema->touchOnSave()) {
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
        $this->triggerEventListener('delete', [$this]);

        if ($id = $this->id()) {
            $this->table()->delete()->where($this->getPrimaryKey() . ' = ' . $id)->run();
        }

        $this->triggerEventListener('deleted', [$this]);
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function set($name, $value)
    {
        $columnName = $this->transformPropertyNameToColumnName($name);

        if ($this->hasColumn($columnName)) {
            $this->changedData[$columnName] = $value;
            return $this;
        }

        $belongsToSchema = $this->getBelongsToSchema($name);

        if ($belongsToSchema && get_class($value) == $belongsToSchema->getModelClassName()) {
            $this->relationData[$name] = $value;
            $this->changedData[$belongsToSchema->getForeignKey()] = $value->id();
            return $this;
        }

        $this->mixedData[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        # from column
        $columnName = $this->transformPropertyNameToColumnName($name);

        if (array_key_exists($columnName, $changedData = $this->getChangedData())) {
            return $changedData[$columnName];
        }

        if (array_key_exists($columnName, $originalData = $this->getOriginalData())) {
            return $originalData[$columnName];
        }

        # lazy loading
        if ($this->exists() && $this->hasColumn($columnName)) {
            return $this->loadColumn($columnName);
        }

        # from relations
        $result = $this->getRelation($name, true, $success);

        if ($success) {
            return $result;
        }

        $mixedData = $this->getMixedData();

        return isset($mixedData[$name])? $mixedData[$name] : null;
    }

    /**
     * @param string $name
     * @param bool $cache
     * @param null $success
     * @return AbstractModel|\Sloths\Db\Model\Collection
     */
    public function getRelation($name, $cache = false, &$success = null)
    {
        $success = true;

        if ($cache && array_key_exists($name, $this->relationData)) {
            return $this->relationData[$name];
        }

        if ($this->hasBelongsTo($name)) {
            $result = $this->getBelongsTo($name);
            $this->relationData[$name] = $result;
            return $result;
        }

        if ($this->hasHasMany($name)) {
            $result = $this->getHasMany($name);
            $this->relationData[$name] = $result;
            return $result;
        }

        if ($this->hasHasOne($name)) {
            $result = $this->getHasOne($name);
            $this->relationData[$name] = $result;
            return $result;
        }

        $success = false;
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return isset($this->data[$this->getPrimaryKey()]);
    }

    /**
     * @return int
     */
    public function id()
    {
        return $this->exists()? $this->data[$this->getPrimaryKey()] : null;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param $method
     * @param $args
     * @return AbstractModel|Collection
     * @throws \BadMethodCallException
     */
    public function __call($method, $args)
    {
        $result = $this->getRelation($method, false, $success);
        if ($success) {
            return $result;
        }

        throw new \BadMethodCallException('Call to undefined method ' . $method);
    }

    /**
     * @return \Sloths\Db\Table
     */
    public function table()
    {
        if (!$this->table) {
            $this->table = $this->getConnectionManager()->table($this->getTableName());
        }

        return $this->table;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = array_merge($this->getOriginalData(), $this->getChangedData(), $this->getMixedData());

        if ($hiddenColumns = $this->getHiddenColumns()) {
            $result = ArrayUtils::except($result, $hiddenColumns);
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

    public function serialize() {
        return serialize($this->getOriginalData());
    }

    /**
     * @param string $data
     */
    public function unserialize($data) {
        $this->data = unserialize($data);
    }

    /**
     * @param null $where
     * @param null $params
     * @param null $columns
     * @return Collection
     */
    private function _all($where = null, $params = null, $columns = null)
    {
        $select = $this->table()->select();

        if ($where) {
            if (is_numeric($where)) {
                $select->where($this->getPrimaryKey() . ' = ' . $where);
            } elseif (is_array($where) && ArrayUtils::hasOnlyInts($where)) {
                $select->where($this->getPrimaryKey() . ' IN (' . implode(', ', $where ) . ')');
            } else {
                call_user_func_array([$select, 'where'], func_get_args());
            }
        }

        if (!$columns) {
            $select->select($this->getDefaultSelectColumns());
        }

        $collectionClassName = $this->collectionClassName;
        return new $collectionClassName($select, $this);
    }

    /**
     * @param null $where
     * @param null $params
     * @return null|AbstractModel
     */
    private function _first($where = null, $params = null)
    {
        $select = $this->table()->select();

        if (is_numeric($where)) {
            $select->where($this->getPrimaryKey() . ' = ' . $where);
        } else {
            call_user_func_array([$select, 'where'], func_get_args());
        }

        $row = $select->first();

        return $row? new static($row) : null;
    }

    /**
     * @param array $data
     * @return static
     */
    public static function create($data = [])
    {
        return new static($data);
    }

    /**
     * @param AbstractModel $model
     * @return Relation\HasManySchema|HasManyThroughSchema
     */
    protected function getHasMayThroughSchemaByModel(AbstractModel $model)
    {
        $allHasManySchema = $this->getAllHasManySchema();

        foreach ($allHasManySchema as $schema) {
            $modelClassName = $schema->getModelClassName();
            if ($schema instanceof HasManyThroughSchema && $model instanceof $modelClassName) {
                return $schema;
            }
        }
    }

    protected function addRelation(AbstractModel $relation)
    {
        $schema = $this->getHasMayThroughSchemaByModel($relation);

        if ($schema) {
            $throughModelName = $schema->getThroughModelClassName();
            $leftKey = $schema->getLeftForeignKey();
            $rightKey = $schema->getRightForeignKey();
            $leftKeyValue = $this->id();
            $rightKeyValue = $relation->id();

            $data = [$leftKey => $leftKeyValue, $rightKey => $rightKeyValue];
            if (!$throughModelName::first($data)) {
                $throughModelName::create($data)->save();
            }

            return $this;
        }

        throw new \InvalidArgumentException('Invalid relation');
    }

    protected function removeRelation(AbstractModel $relation)
    {
        $schema = $this->getHasMayThroughSchemaByModel($relation);
        if ($schema) {
            $throughModelName = $schema->getThroughModelClassName();
            $leftKey = $schema->getLeftForeignKey();
            $rightKey = $schema->getRightForeignKey();
            $leftKeyValue = $this->id();
            $rightKeyValue = $relation->id();

            $data = [$leftKey => $leftKeyValue, $rightKey => $rightKeyValue];
            if ($throughModel = $throughModelName::first($data)) {
                $throughModel->delete();
            }

            return $this;
        }

        throw new \InvalidArgumentException('Invalid relation');
    }

    public function add($relations)
    {
        if ($relations instanceof AbstractModel) {
            $relations = [$relations];
        }

        if (!is_array($relations) && !$relations instanceof Collection) {
            $relations = [$relations];
        }

        $connection = $this->getConnectionManager()->getWriteConnection();
        $connection->beginTransaction();

        try {
            foreach ($relations as $relation) {
                $this->addRelation($relation);
            }
            $connection->commit();

        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        return $this;
    }

    public function remove($relations)
    {
        if ($relations instanceof AbstractModel) {
            $relations = [$relations];
        }

        if (!is_array($relations) && !$relations instanceof Collection) {
            $relations = [$relations];
        }

        $connection = $this->getConnectionManager()->getWriteConnection();
        $connection->beginTransaction();

        try {
            foreach ($relations as $relation) {
                $this->removeRelation($relation);
            }
            $connection->commit();

        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        return $this;
    }

    public function sync($relations)
    {
        if ($relations instanceof AbstractModel) {
            $relations = [$relations];
        }

        if (!is_array($relations) && !$relations instanceof Collection) {
            $relations = [$relations];
        }

        $connection = $this->getConnectionManager()->getWriteConnection();
        $connection->beginTransaction();

        try {
            # remove non existing first
            $ids = [];
            foreach ($relations as $relation) {
                $ids[] = $relation->id();
            }

            $this->remove($relation::all(sprintf('%s NOT IN(?)', $relation->getPrimaryKey()), $ids));

            # and then add
            $this->add($relations);

            $connection->commit();

        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     * @throws \BadMethodCallException
     */
    public static function __callStatic($method, $args)
    {
        if (in_array($method, ['all', 'first'])) {
            return call_user_func_array([new static(), '_' . $method], $args);
        }

        throw new \BadMethodCallException('Call to undefined method ' . $method);
    }
}