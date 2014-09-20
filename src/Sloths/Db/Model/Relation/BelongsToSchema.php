<?php

namespace Sloths\Db\Model\Relation;

class BelongsToSchema extends AbstractSchema
{
    /**
     * @var string
     */
    protected $foreignKey;

    /**
     * @var bool
     */
    protected $touchOnSave;

    /**
     * @param string $modelClassName
     * @param string $foreignKey
     * @param bool $touchOnSave
     */
    public function __construct($modelClassName, $foreignKey, $touchOnSave = false)
    {
        $this->modelClassName = $modelClassName;
        $this->foreignKey = $foreignKey;
        $this->touchOnSave = $touchOnSave;
    }

    /**
     * @return string
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * @return bool
     */
    public function touchOnSave()
    {
        return $this->touchOnSave;
    }
}