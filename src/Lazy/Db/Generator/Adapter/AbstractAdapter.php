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

    public function parseConstraints2(array &$schemas, $namespace = '')
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

                    if (!isset($manyToManyMaps[$leftTable])) {
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

    public function parseConstraints(array $schemas, $namespace = '')
    {
        if ($namespace) {
            $namespace .= '\\\\';
        }

        $constraints = [];
        $manyToOne = [];
        foreach ($schemas as $table => $schema) {
            $manyToOne[$table] = $this->listConstraints($table);
        }

        foreach ($manyToOne as $table => $schema) {
            if (!isset($constraints[$table])) {
                $constraints[$table] = ['manyToOne' => $schema];
            }
        }

        foreach ($constraints as $table => $schema) {
            foreach ($schema['manyToOne'] as $refKey => $refTable) {
                if (!isset($constraints[$refTable])) {
                    $constraints[$refTable] = [
                        'oneToMany' => []
                    ];
                }

                $constraints[$refTable]['oneToMany'][$table] = $refKey;
            }
        }

        $manyToMany = [];
        foreach ($constraints as $table => $schema) {
            if ($schema['manyToOne']) {
                foreach ($schema['manyToOne'] as $leftKey => $leftTable) {
                    foreach ($schema['manyToOne'] as $rightKey => $rightTable) {
                        if ($leftKey == $rightKey) {
                            continue;
                        }

                        if (!isset($manyToMany[$leftTable])) {
                            $manyToMany[$leftTable] = [];
                        }

                        $manyToMany[$leftTable][$rightTable] = [
                            'leftKey' => $leftKey,
                            'rightKey' => $rightKey,
                            'through' => $table
                        ];
                    }
                }
            }
        }

        foreach ($manyToMany as $table => $schema) {
            if (!isset($constraints[$table])) {
                $constraints[$table] = [];
            }

            $constraints[$table]['manyToMany'] = $schema;
        }

        foreach ($constraints as $table => $schema) {
            if (isset($schema['manyToOne'])) {
                $schemas[$table]['manyToOne'] = [];
                foreach ($schema['manyToOne'] as $refKey => $refTable) {
                    $key = preg_replace('/_id$/', '', $refKey);
                    $key = Inflector::classify($key);
                    $model = $namespace . Inflector::classify(Inflector::singularize($refTable));
                    $schemas[$table]['manyToOne'][$key] = [
                        'model' => $model,
                        'key'   => $refKey
                    ];
                }
            }

            if (isset($schema['oneToMany'])) {
                $schemas[$table]['oneToMany'] = [];
                foreach ($schema['oneToMany'] as $refTable => $refKey) {
                    $key = Inflector::pluralize($refTable);
                    $key = Inflector::classify($key);
                    $model = $namespace . Inflector::classify(Inflector::singularize($refTable));
                    $schemas[$table]['oneToMany'][$key] = [
                        'model' => $model,
                        'key'   => $refKey
                    ];
                }
            }

            if (isset($schema['manyToMany'])) {
                $schemas[$table]['manyToMany'] = [];
                foreach ($schema['manyToMany'] as $rightTable => $schema) {
                    $key = Inflector::classify($rightTable);

                    if (isset($schemas[$table]['oneToMany'][$key])) {
                        continue;
                    }

                    $throughModel = $namespace . Inflector::classify(Inflector::singularize($schema['through']));
                    $schemas[$table]['manyToMany'][$key] = [
                        'leftKey' => $schema['leftKey'],
                        'rightKey' => $schema['rightKey'],
                        'through' => $throughModel
                    ];
                }
            }
        }

        return $schemas;
    }
}