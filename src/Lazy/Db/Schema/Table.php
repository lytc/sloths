<?php

namespace Lazy\Db\Schema;

use Lazy\Db\Sql\Select;
use Lazy\Db\Connection;

class Table
{
    protected $name;
    protected $connection;
    protected $dbName;
    protected $columns;
    protected $hasManyConstraints;
    protected $hasOneConstraints;
    protected $belongsToConstraints;
    protected $hasManyThroughConstraints;

    protected static $caches = [];

    public static function fromCache($name, Connection $connection)
    {
        if (!isset(static::$caches[$name])) {
            static::$caches[$name] = new static($name, $connection);
        }

        return static::$caches[$name];
    }

    public function __construct($name, Connection $connection)
    {
        $this->name = $name;
        $this->connection = $connection;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPrimaryKeyColumn()
    {
        foreach ($this->getColumns() as $name => $meta) {
            if ($meta['isPrimaryKey']) {
                return $name;
            }
        }
    }

    public function getDbName()
    {
        if (!$this->dbName) {
            $this->dbName = $this->connection->query("SELECT DATABASE()")->fetchColumn();
        }
        return $this->dbName;
    }

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
                if (static::fromCache($row['TABLE_NAME'], $this->connection)->getPrimaryKeyColumn() == $row['COLUMN_NAME']) {
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
                if (static::fromCache($row['TABLE_NAME'], $this->connection)->getPrimaryKeyColumn() != $row['COLUMN_NAME']) {
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

    public function getHasManyThroughConstraints()
    {
        if (null === $this->hasManyThroughConstraints) {
            $constraints = [];
            $hasManyConstraints = $this->getHasManyConstraints();
            $belongsToTables = array_column($this->getBelongsToConstraints(), 'table');

            foreach ($hasManyConstraints as $tableName => $hasManyMeta) {
                $table = static::fromCache($tableName, $this->connection);
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