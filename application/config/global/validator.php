<?php

$this
    ->setTranslator($this->getApplication()->translator->validators)

    ->addRules([
        'unique' => function($input, $tableName, $columnName = 'name', $ignore = null) {
                if ($tableName instanceof \Sloths\Db\Model\AbstractModel) {
                    $collection = $tableName::all();
                    if ($tableName->exists()) {
                        $collection->where($tableName::getPrimaryKey() . ' != ' . $tableName->id());
                    }

                    $tableName = $collection;

                }

                if ($tableName instanceof \Sloths\Db\Model\Collection) {
                    $tableName->where($columnName, $input);
                    $exists = !!$collection->foundRows();
                } else {

                    $database = $this->getApplication()->database;
                    $select = new \Sloths\Db\Sql\Select();
                    $select->table($tableName)
                        ->select('COUNT(*)')
                        ->where($columnName, $input);

                    if ($ignore) {
                        $select->where('id != ' . $ignore->id());
                    }

                    $stmt = $database->run($select);
                    $exists = !!$stmt->fetchColumn();

                }

                if ($exists) {
                    return ['":input" has already been taken', ['input' => $input]];
                }

                return true;
            },

        'password' => function($password) {
                $len = strlen($password);

                if ($len >= 8                               // at least 8 characters
                    && preg_match('/[a-z]/', $password)     // lowercase
                    && preg_match('/[A-Z]/', $password)     // uppercase
                    && preg_match('/[0-9]/', $password)     // number
                    && preg_match('/[^\w]/', $password)     // symbol
                ) {
                    return true;
                }

                return 'must be at least 8 characters. Must contains lowercase (a-z), uppercase (A-Z), number (0-9) and symbol';
            }
    ])
;