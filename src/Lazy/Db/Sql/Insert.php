<?php

namespace Lazy\Db\Sql;

use Lazy\Db\Db;

class Insert extends Replace
{
    /**
     * @var string
     */
    protected $spec = 'INSERT';

    /**
     * @var array
     */
    protected $onDuplicateKeyUpdate;

    /**
     * @param bool $state
     * @return $this
     */
    public function ignore($state = true)
    {
        return $this->toggleOption('IGNORE', $state);
    }

    /**
     * @param array $values
     */
    public function onDuplicateKeyUpdate(array $values)
    {
        $this->onDuplicateKeyUpdate = $values;
    }

    /**
     * @return string
     */
    public function toString()
    {
        $sql = parent::toString();

        if ($this->onDuplicateKeyUpdate) {
            $sql .= ' ON DUPLICATE KEY UPDATE ';
            $set = [];

            foreach ($this->onDuplicateKeyUpdate as $key => $value) {
                $set[] = $key . ' = ' . Db::quote($value);
            }

            $sql .= implode(', ', $set);
        }

        return $sql;
    }
}