<?php

namespace Sloths\Db\Model;

use Sloths\Misc\StringUtils;
use Sloths\Misc\Inflector;

trait TransformNameTrait
{
    /**
     * @param string $tableName
     * @return string
     */
    public function transformTableNameToClassName($tableName)
    {
        return Inflector::singularize(Inflector::classify($tableName));
    }

    /**
     * @param string $className
     * @return string
     */
    public function transformClassNameToTableName($className)
    {
        $tableName = StringUtils::getClassBaseName($className);
        $tableName = Inflector::underscore($tableName);
        $tableName = Inflector::pluralize($tableName);

        return $tableName;
    }

    /**
     * @param string $columnName
     * @return string
     */
    public function transformColumnNameToPropertyName($columnName)
    {
        return Inflector::camelize($columnName);
    }

    /**
     * @param string $propertyName
     * @return string
     */
    public function transformPropertyNameToColumnName($propertyName)
    {
        return Inflector::underscore($propertyName);
    }

    /**
     * @param string $name
     * @return string
     */
    public function transformToForeignKeyColumnName($name)
    {
        return Inflector::underscore(Inflector::singularize($name)) . '_id';
    }
}