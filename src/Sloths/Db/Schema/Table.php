<?php

namespace Sloths\Db\Schema;

use Sloths\Db\Sql\Select;
use Sloths\Db\Connection;

class Table
{
    /**
     * @var
     */
    protected $name;

    /**
     * @var \Sloths\Db\Connection
     */
    protected $connection;

    /**
     * @var
     */
    protected $columns;

    /**
     * @var
     */
    protected $hasManyConstraints;

    /**
     * @var
     */
    protected $hasOneConstraints;

    /**
     * @var
     */
    protected $belongsToConstraints;

    /**
     * @var
     */
    protected $hasManyThroughConstraints;

    /**
     * @var array
     */
    protected static $caches = [];

    /**
     * @param string $name
     * @param Connection $connection
     * @return mixed
     */
    public static function fromCache($name, Connection $connection)
    {
        if (!isset(static::$caches[$name])) {
            static::$caches[$name] = new static($name, $connection);
        }

        return static::$caches[$name];
    }

    /**
     * @param string $name
     * @param Connection $connection
     */
    public function __construct($name, Connection $connection)
    {
        $this->name = $name;
        $this->connection = $connection;
    }

    protected function _fromCache($tableName)
    {
        return static::fromCache($tableName, $this->connection);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPrimaryKeyColumn()
    {
        foreach ($this->getColumns() as $name => $meta) {
            if ($meta['isPrimaryKey']) {
                $result = $name;
                break;
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getDbName()
    {
        return $this->connection->getDbName();
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        if (!$this->columns) {
            $query = "SHOW COLUMNS FROM `{$this->name}`";
            $stmt = $this->connection->query($query);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $columns = [];

            foreach ($rows as $row) {
                $name = $row['Field'];
                preg_match('/^([\w_]+)/', $row['Type'], $matches);

                $columns[$name] = [
                    'name'              => $name,
                    'type'              => $matches[0],
                    'isPrimaryKey'      => $row['Key'] == 'PRI',
                    'isAutoIncrement'   => $row['Extra'] == 'auto_increment'
                ];
            }

            $this->columns = $columns;
        }

        return $this->columns;
    }

    /**
     * @return array
     */
    public function getHasManyConstraints()
    {
        if (null === $this->hasManyConstraints) {
            $select = new Select('information_schema.KEY_COLUMN_USAGE');
            $select->select('TABLE_NAME, COLUMN_NAME')
                ->where('TABLE_SCHEMA = ?', $this->getDbName())
                ->where('REFERENCED_TABLE_NAME = ?', $this->name)
            ;

            $rows = $this->connection->query($select->toString())->fetchAll(\PDO::FETCH_ASSOC);
            $constraints = [];

            foreach ($rows as $row) {
                # filter again, if the column is primary key, it is one to one
                if ($this->_fromCache($row['TABLE_NAME'])->getPrimaryKeyColumn() == $row['COLUMN_NAME']) {
                    continue;
                }

                $constraints[$row['TABLE_NAME']] = [
                    'table'         => $row['TABLE_NAME'],
                    'foreignKey'    => $row['COLUMN_NAME'],
                ];
            }

            $this->hasManyConstraints = $constraints;
        }

        return $this->hasManyConstraints;
    }

    /**
     * @return array
     */
    public function getHasOneConstraints()
    {
        if (null == $this->hasOneConstraints) {
            $select = new Select('information_schema.KEY_COLUMN_USAGE');
            $select->select('TABLE_NAME, COLUMN_NAME')
                ->where('TABLE_SCHEMA = ?', $this->getDbName())
                ->where('REFERENCED_TABLE_NAME = ?', $this->name)
            ;

            $rows = $this->connection->query($select->toString())->fetchAll(\PDO::FETCH_ASSOC);
            $constraints = [];

            foreach ($rows as $row) {
                # filter again, if the column isn't primary key, it is one to many
                if ($this->_fromCache($row['TABLE_NAME'], $this->connection)->getPrimaryKeyColumn() != $row['COLUMN_NAME']) {
                    continue;
                }

                $constraints[$row['TABLE_NAME']] = [
                    'table'         => $row['TABLE_NAME'],
                    'foreignKey'    => $row['COLUMN_NAME'],
                ];
            }

            $this->hasOneConstraints = $constraints;
        }

        return $this->hasOneConstraints;
    }

    /**
     * @return array
     */
    public function getBelongsToConstraints()
    {
        if (null === $this->belongsToConstraints) {
            $select = new Select('information_schema.KEY_COLUMN_USAGE');
            $select->select('COLUMN_NAME, REFERENCED_TABLE_NAME')
                ->where('TABLE_SCHEMA = ?', $this->getDbName())
                ->where('TABLE_NAME = ?', $this->name)
                ->where('REFERENCED_TABLE_NAME IS NOT NULL')
            ;

            $rows = $this->connection->query($select->toString())->fetchAll(\PDO::FETCH_ASSOC);
            $constraints = [];

            foreach ($rows as $row) {
                $constraints[$row['COLUMN_NAME']] = [
                    'table'         => $row['REFERENCED_TABLE_NAME'],
                    'foreignKey'    => $row['COLUMN_NAME'],
                ];
            }

            $this->belongsToConstraints = $constraints;
        }

        return $this->belongsToConstraints;
    }

    /**
     * @return array
     */
    public function getHasManyThroughConstraints()
    {
        if (null === $this->hasManyThroughConstraints) {
            $constraints = [];
            $hasManyConstraints = $this->getHasManyConstraints();
            $belongsToTables = array_column($this->getBelongsToConstraints(), 'table');

            foreach ($hasManyConstraints as $tableName => $hasManyMeta) {
                $table = $this->_fromCache($tableName);
                $belongsToConstraints = $table->getBelongsToConstraints();

                foreach ($belongsToConstraints as $columnName => $belongToMeta) {
                    if ($belongToMeta['table'] == $this->name || array_key_exists($belongToMeta['table'], $hasManyConstraints)) {
                        continue;
                    }

                    $constraint = [
                        'throughTableName' => $tableName,
                        'tableName' => $belongToMeta['table'],
                        'leftKey' => $hasManyMeta['foreignKey'],
                        'rightKey' => $belongToMeta['foreignKey']
                    ];

                    if (in_array($belongToMeta['table'], $belongsToTables)) {
                        $constraint['hasBelongsTo'] = true;
                    }

                    $constraints[] = $constraint;
                }
            }

            $this->hasManyThroughConstraints = $constraints;
        }

        return $this->hasManyThroughConstraints;
    }
}