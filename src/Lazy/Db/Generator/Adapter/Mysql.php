<?php

namespace Lazy\Db\Generator\Adapter;

class Mysql extends AbstractAdapter
{
    protected $numericTypes = array(
        'int', 'smallint', 'mediumint', 'bigint', 'double', 'tinyint', 'float', 'double', 'decimal'
    );
    public function listTable()
    {
        $query = "SHOW TABLES";
        $stmt = $this->connection->query($query);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function listColumnSchemas($table)
    {
        $query = "SHOW COLUMNS FROM `$table`";
        $stmt = $this->connection->query($query);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $result = array();
        foreach ($rows as $row) {
            if (preg_match('/^(\w+)\((\d+)\)/', $row['Type'], $matches)) {
                $type = $matches[1];
                $length = $matches[2];
            } elseif (preg_match('/^(\w+)\((\d+),(\d+)\)/', $row['Type'], $matches)) {
                $type = $matches[1];
                $length = $matches[2];
                $decimals = $matches[3];
            } else {
                $type = $row['Type'];
            }

            $schema = array('type' => $type);

            if (isset($length)) {
                $schema['length'] = (int) $length;
            }

            if (isset($decimals)) {
                $schema['decimals'] = (int) $decimals;
            }

            if (in_array($type, $this->numericTypes)) {
                if (preg_match('/unsigned$/', $row['Type'])) {
                    $schema['unsigned'] = true;
                }
            }

            $schema['default'] = $row['Default'];

            if ($row['Extra'] == 'auto_increment') {
                $schema['auto'] = true;
            }

            $schema['nullable'] = $row['Null'] == 'YES';
            $result[$row['Field']] = $schema;
        }

        return $result;
    }

    public function listConstraints($table)
    {
        $result = array();
        $query = "SHOW CREATE TABLE `$table`";
        $stmt = $this->connection->query($query);

        $pattern = '/CONSTRAINT (.*) FOREIGN KEY \(`(?<foreignKey>.*)`\) REFERENCES `(?<refTable>.*)` \((`(?<primaryKey>.*)`)\)/';

        preg_match_all($pattern, $stmt->fetchColumn(1), $matches, PREG_SET_ORDER);
        if ($matches) {
            foreach ($matches as $item) {
                $result[$item['foreignKey']] = $item['refTable'];
            }
        }

        return $result;
    }
}