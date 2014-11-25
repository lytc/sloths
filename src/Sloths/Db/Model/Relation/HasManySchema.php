<?php

namespace Sloths\Db\Model\Relation;

class HasManySchema extends AbstractSchema
{
    /**
     * @var string
     */
    protected $foreignKey;

    /**
     * @param string $modelClassName
     * @param string $foreignKey
     */
    public function __construct($modelClassName, $foreignKey)
    {
        $this->modelClassName = $modelClassName;
        $this->foreignKey = $foreignKey;
    }

    /**
     * @return string
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }
}