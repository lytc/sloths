<?php

namespace Lazy\Db\Generator\Adapter;

use Doctrine\Common\Inflector\Inflector;
use Lazy\Db\Connection;

abstract class AbstractAdapter
{
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    abstract public function listTable();
    abstract public function listColumnSchemas($table);
    abstract public function listConstraints($table);

    public function parseConstraints(array &$schemas, $namespace = '')
    {
        if ($namespace) {
            $namespace .= '\\\\';
        }
        $oneToManyMaps = array();

        foreach ($schemas as $table => $schema) {
            if (!isset($schema['manyToOne'])) {
                continue;
            }

            $manyToOne = $schema['manyToOne'];
            foreach ($manyToOne as $foreignKey => $refTable) {
                if (!isset($oneToManyMaps[$refTable])) {
                    $oneToManyMaps[$refTable] = array();
                }
                $oneToManyMaps[$refTable][$foreignKey] = $table;
            }
        }

        foreach ($oneToManyMaps as $table => $oneToMany) {
            $schemas[$table]['oneToMany'] = $oneToMany;
        }

        $manyToManyMaps = array();
        foreach ($schemas as $table => $schema) {
            if (!isset($schema['manyToOne'])) {
                continue;
            }

            $manyToOne = $schema['manyToOne'];
            foreach ($manyToOne as $leftKey => $leftTable) {
                foreach ($manyToOne as $rightKey => $rightTable) {
                    if ($leftTable == $rightTable) {
                        continue;
                    }

                    if (!isset ($manyToManyMaps[$leftTable])) {
                        $manyToManyMaps[$leftTable] = array();
                    }

                    if (isset($oneToManyMaps[$leftTable][$rightTable])) {
                        continue;
                    }

                    $manyToManyMaps[$leftTable][$rightTable] = array(
                        'leftKey' => $leftKey,
                        'rightKey' => $rightKey,
                        'through' => $table
                    );
                }
            }
        }

        foreach ($manyToManyMaps as $table => $manyToMany) {
            $schemas[$table]['manyToMany'] = $manyToMany;
        }

        foreach ($schemas as $table => $schema) {
            if (isset($schema['oneToMany'])) {
                $oneToMany = array();
                foreach ($schema['oneToMany'] as $foreignKey => $refTable) {
                    $refName = Inflector::classify(Inflector::classify(Inflector::pluralize($refTable)));
                    $oneToMany[$refName] = array(
                        'model' => $namespace . Inflector::classify(Inflector::singularize($refTable)),
                        'key' => $foreignKey
                    );
                }
                $schemas[$table]['oneToMany'] = $oneToMany;
            }

            if (isset($schema['manyToOne'])) {
                $manyToOne = array();
                foreach ($schema['manyToOne'] as $foreignKey => $refTable) {
                    $refName = Inflector::classify(Inflector::singularize(preg_replace('/_id$/', '', $foreignKey)));
                    $manyToOne[$refName] = array(
                        'model' => $namespace . Inflector::classify(Inflector::singularize($refTable)),
                        'key' => $foreignKey
                    );
                }
                $schemas[$table]['manyToOne'] = $manyToOne;
            }

            if (isset($schema['manyToMany'])) {
                $manyToMany = array();
                foreach ($schema['manyToMany'] as $refTable => $manyToManySchema) {
                    $refName = Inflector::classify(Inflector::classify(Inflector::pluralize($refTable)));
                    $manyToMany[$refName] = array(
                        'model' => $namespace . Inflector::classify(Inflector::singularize($refTable)),
                        'through' => $namespace . Inflector::classify(Inflector::singularize($manyToManySchema['through'])),
                        'leftKey' => $manyToManySchema['leftKey'],
                        'rightKey' => $manyToManySchema['rightKey'],
                    );
                }
                $schemas[$table]['manyToMany'] = $manyToMany;
            }
        }
    }
}