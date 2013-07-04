<?php

namespace Lazy\Db;

use Doctrine\Common\Inflector\Inflector;
use Lazy\Db\Generator\Exception as GeneratorException;
use Lazy\Db\Generator\Adapter\AbstractAdapter;

class Generator
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var AbstractAdapter
     */
    protected $driver;

    /**
     * @var string
     */
    protected $namespace = '';

    /**
     * @var bool
     */
    protected $generateAbstractModel = true;

    /**
     * @var string
     */
    protected $rootAbstractModelClassName;

    /**
     * @var string
     */
    protected $directory = '.';

    /**
     * @var array
     */
    protected $schemas;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        $driverName = $connection->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $driverClass = __NAMESPACE__ . '\\Generator\\Adapter\\' . ucfirst($driverName);

        if (!class_exists($driverClass)) {
            throw new GeneratorException(sprintf('Driver not found for %s', $driverName));
        }

        $this->driver = new $driverClass($this->connection);
    }

    public function setDirectory($directory)
    {
        $this->directory = $directory;
        return $this;
    }

    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    public function setRootAbstractModelClassName($name)
    {
        $this->rootAbstractModelClassName = $name;
        return $this;
    }

    public function setGenerateAbstractModel($state)
    {
        $this->generateAbstractModel = !!$state;
        return $this;
    }

    public function generate()
    {
        $tables = $this->driver->listTable();
        $schemas = array();
        foreach ($tables as $table) {
            $schemas[$table] = array(
                'columns' => $this->driver->listColumnSchemas($table),
                'manyToOne' => $this->driver->listConstraints($table)
            );
        }


        $this->driver->parseConstraints($schemas, $this->namespace);
//        echo json_encode($schemas); exit;

        $this->schemas = $schemas;

        # create dir
        $directory = $this->getDirectory();
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        # cleanup abstract model directory if exists
        $rootAbstractModelClassName = $this->rootAbstractModelClassName;

        $abstractModelDirectory = $directory . '/AbstractModel';

        if (file_exists($abstractModelDirectory)) {
            $dir = dir($abstractModelDirectory);
            while (false !== ($f = $dir->read())) {
                if ('.' == $f || '..' == $f) {
                    continue;
                }

                if ($this->generateAbstractModel && $rootAbstractModelClassName && $rootAbstractModelClassName . '.php' == $f) {
                    continue;
                }

                unlink($abstractModelDirectory  .'/' . $f);
            }

            $dir->close();

            if (!$this->generateAbstractModel) {
                unlink($abstractModelDirectory);
            }
        } elseif ($this->generateAbstractModel) {
            mkdir($abstractModelDirectory);
        }

        $this->generateRootAbstractModel();
        $this->generateAbstractModel();
        $this->generateModel();
    }

    protected function getDirectory()
    {
        return $this->directory . '/' . str_replace('\\', '/', $this->namespace);
    }

    protected function generateRootAbstractModel()
    {
        if (!$this->generateAbstractModel || !$this->rootAbstractModelClassName) {
            return;
        }

        $namespace = $this->namespace;
        $rootAbstractModelClassName = $this->rootAbstractModelClassName;
        $abstractModelDirectory = $this->getDirectory() . '/AbstractModel';
        $fileName = $abstractModelDirectory . '/' . $rootAbstractModelClassName . '.php';

        if (file_exists($fileName)) {
            return;
        }

        $classContentParts = array('<?php');

        if ($namespace) {
            $classContentParts[] = "namespace $namespace\\AbstractModel;";
        }

        $classContentParts[] = "class $rootAbstractModelClassName extends \\Lazy\\Db\\AbstractModel";
        $classContentParts[] = '{';
        $classContentParts[] = '}';

        $classContent = implode(PHP_EOL, $classContentParts);
        file_put_contents($fileName, $classContent);
    }

    public function generateAbstractModel()
    {
        if (!$this->generateAbstractModel) {
            return;
        }

        $namespace = $this->namespace;
        $rootAbstractModelClassName = $this->rootAbstractModelClassName;
        $abstractModelDirectory = $this->getDirectory() . '/AbstractModel';

        foreach ($this->schemas as $tableName => $schema) {
            $classContentParts = array('<?php');

            if ($namespace) {
                $classContentParts[] = "namespace $namespace\\AbstractModel;" . PHP_EOL;
            }

            $modelClassName = Inflector::classify(Inflector::singularize($tableName));

            # properties columns
            $properties = array();
            foreach ($schema['columns'] as $columnName => $columnSchema) {
                $properties[] = ' * @property ' . $columnSchema['type'] . ' ' . Inflector::camelize($columnName);
            }

            # properties one to many
            if (!empty($schema['oneToMany'])) {
                foreach ($schema['oneToMany'] as $refName => $oneToManySchema) {
                    $properties[] = ' * @property \Lazy\Db\Collection ' . $refName;
                }
            }

            # properties many to one
            if (!empty($schema['manyToOne'])) {
                foreach ($schema['manyToOne'] as $refName => $manyToOneSchema) {
                    $properties[] = ' * @property \\' . str_replace('\\\\', '\\', $manyToOneSchema['model']) . ' ' . $refName;
                }
            }

            # properties many to many
            if (!empty($schema['manyToMany'])) {
                foreach ($schema['manyToMany'] as $refName => $manyToManySchema) {
                    $properties[] = ' * @property \Lazy\Db\Collection ' . $refName;
                }
            }

            $classContentParts[] = '/**' . PHP_EOL . implode(PHP_EOL, $properties) . PHP_EOL . ' */';

            $classContentParts[] = "abstract class Abstract$modelClassName extends " . ($rootAbstractModelClassName? $rootAbstractModelClassName : '\Lazy\Db\AbstractModel');
            $classContentParts[] = '{';

            # columns
            $classContentParts[] = '    protected static $columns = array(';
            foreach ($schema['columns'] as $columnName => $columnSchemas) {
                $classContentParts[] = "        '$columnName' => array(";
                foreach ($columnSchemas as $key => $value) {
                    if ('type' == $key) {
                        $value = 'self::TYPE_' . strtoupper($value);
                    } else {
                        switch (gettype($value)) {
                            case 'boolean':
                                $value = $value? 'true' : 'false';
                                break;

                            case 'integer':
                                $value = $value;
                                break;

                            case 'NULL':
                                $value = 'null';
                                break;

                            default:
                                $value = "'$value'";
                        }
                    }
                    $spaces = str_pad('', 10 - strlen($key));
                    $classContentParts[] = "            '$key'$spaces=> $value,";
                }
                $classContentParts[] = '        ),';
            }
            $classContentParts[] = '    );' . PHP_EOL;

            # one to many
            if (!empty($schema['oneToMany'])) {
                $classContentParts[] = '    protected static $oneToMany = array(';
                foreach ($schema['oneToMany'] as $refName => $oneToMany) {
                    $classContentParts[] = "        '$refName' => array(";
                    foreach ($oneToMany as $key => $value) {
                        $spaces = str_pad('', 9 - strlen($key));
                        $classContentParts[] = "            '$key' $spaces=> '$value',";
                    }
                    $classContentParts[] = '        ),';
                }
                $classContentParts[] = '    );' . PHP_EOL;
            }

            # many to one
            if (!empty($schema['manyToOne'])) {
                $classContentParts[] = '    protected static $manyToOne = array(';
                foreach ($schema['manyToOne'] as $refName => $manyToOne) {
                    $classContentParts[] = "        '$refName' => array(";
                    foreach ($manyToOne as $key => $value) {
                        $spaces = str_pad('', 9 - strlen($key));
                        $classContentParts[] = "            '$key' $spaces=> '$value',";
                    }
                    $classContentParts[] = '        ),';
                }
                $classContentParts[] = '    );' . PHP_EOL;
            }

            # many to many
            if (!empty($schema['manyToMany'])) {
                $classContentParts[] = '    protected static $manyToMany = array(';
                foreach ($schema['manyToMany'] as $refName => $manyToMany) {
                    $classContentParts[] = "        '$refName' => array(";
                    foreach ($manyToMany as $key => $value) {
                        $spaces = str_pad('', 9 - strlen($key));
                        $classContentParts[] = "            '$key' $spaces=> '$value',";
                    }
                    $classContentParts[] = '        ),';
                }
                $classContentParts[] = '    );' . PHP_EOL;
            }

            $classContentParts[] = '}';

            $fileName = $abstractModelDirectory . '/Abstract' . $modelClassName . '.php';
            $classContent = implode(PHP_EOL, $classContentParts);
            file_put_contents($fileName, $classContent);
        }
    }

    public function generateModel()
    {
        $namespace = $this->namespace;

        foreach ($this->schemas as $tableName => $schema) {
            $modelClassName = Inflector::classify(Inflector::singularize($tableName));
            $fileName = $this->getDirectory() . '/' . $modelClassName . '.php';

            if (file_exists($fileName)) {
                continue;
            }

            $classContentParts = array('<?php');

            if ($namespace) {
                $classContentParts[] = "namespace $namespace;";
            }

            if ($this->generateAbstractModel) {
                $classContentParts[] = "use $namespace\\AbstractModel\\Abstract$modelClassName;" . PHP_EOL;
                $classContentParts[] = "class $modelClassName extends Abstract$modelClassName";
            } else {
                $classContentParts[] = "use Lazy\Db\AbstractModel;" . PHP_EOL;
                $classContentParts[] = "class $modelClassName extends AbstractModel";
            }

            $classContentParts[] = '{';
            $classContentParts[] = '}';

            $classContent = implode(PHP_EOL, $classContentParts);
            file_put_contents($fileName, $classContent);
        }
    }
}