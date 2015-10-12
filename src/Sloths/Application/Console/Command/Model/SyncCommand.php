<?php

namespace Sloths\Application\Console\Command\Model;

use Sloths\Db\Connection;
use Sloths\Misc\Inflector;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sloths\Application\Console\Command\Command;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlock\Tag\MethodTag;
use Zend\Code\Generator\DocBlock\Tag\PropertyTag;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\PropertyGenerator;
use Zend\Code\Generator\PropertyValueGenerator;
use Zend\Code\Generator\ValueGenerator;

class SyncCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('model:sync')
            ->setDescription('Sync application model with database schema')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getSloths()->database->getWriteConnection();
        $tables = $connection->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            if ($table == $this->getSloths()->migrator->getTable()) {
                continue;
            }

            $this->syncForTable($table, $output);
        }
    }

    protected function syncForTable($table, OutputInterface $output)
    {
        $connection = $this->getSloths()->database->getWriteConnection();
        $dbName = $connection->getDbName();

        $directory = $this->getSloths()->getPath('models');
        $className = Inflector::classify(Inflector::singularize($table));

        # generate abstract model class
        $abstractClassName = Inflector::classify('Abstract' . $className);
        $classGenerator = new ClassGenerator();
        $classGenerator
            ->setNamespaceName('Application\\Model\\Base')
            ->addUse('Sloths\\Db\\Model\\AbstractModel')
            ->setAbstract(true)
            ->setName($abstractClassName)
            ->setExtendedClass('AbstractModel')
        ;

        # docblock generator
        $docBlockGenerator = new DocBlockGenerator();
        $classGenerator->setDocBlock($docBlockGenerator);

        # tableName property
        $classGenerator->addProperty('tableName', $table, PropertyGenerator::FLAG_PROTECTED);

        # primaryKey property
        $primaryKey = $connection->query("SHOW COLUMNS from `$table` WHERE `Key` = 'PRI'")
            ->fetchColumn();
        $classGenerator->addProperty('primaryKey', $primaryKey, PropertyGenerator::FLAG_PROTECTED);

        # columns property
        $classGenerator->addPropertyFromGenerator(
            $this->getColumnsPropertyGenerator($table, $dbName, $connection, $docBlockGenerator)
        );

        # belongsTo property
        $classGenerator->addPropertyFromGenerator(
            $this->getBelongsToPropertyGenerator($table, $dbName, $connection, $docBlockGenerator)
        );

        # hasMany property
        $classGenerator->addPropertyFromGenerator(
            $this->getHasManyPropertyGenerator($table, $dbName, $connection, $docBlockGenerator)
        );

        $fileGenerator = new FileGenerator();
        $fileGenerator->setClass($classGenerator);
        $file = $directory . '/Base/' . $abstractClassName . '.php';
        file_put_contents($file, $fileGenerator->generate());

        $output->writeln(sprintf('==> %s: %s', $table, $abstractClassName));

        # generate model class
        $file = $directory . '/' . $className . '.php';
        if (!is_file($file)) {
            $classGenerator = new ClassGenerator();
            $classGenerator
                ->setNamespaceName('Application\\Model')
                ->addUse('Application\\Model\\Base\\' . $abstractClassName)
                ->setName($className)
                ->setExtendedClass($abstractClassName)
            ;

            $fileGenerator = new FileGenerator();
            $fileGenerator->setClass($classGenerator);
            file_put_contents($file, $fileGenerator->generate());

            $output->writeln(sprintf('==> %s: %s', $table, $className));
        }
    }

    protected function getColumnsPropertyGenerator($table, $dbName, $connection, DocBlockGenerator $docBlockGenerator)
    {
        # columns
        $propertyGenerator = new PropertyGenerator('columns');
        $propertyGenerator->setVisibility('protected');

        $sql = "
          SELECT COLUMN_NAME, DATA_TYPE, COLUMN_KEY
          FROM `information_schema`.`COLUMNS`
          WHERE
            `TABLE_SCHEMA` = '$dbName'
            AND `TABLE_NAME` = '$table'
        ";

        $value = [];
        $columns = $connection->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($columns as $column) {
            $typeGenerator = new ValueGenerator('self::' . strtoupper($column['DATA_TYPE']), ValueGenerator::TYPE_CONSTANT);
            $value[$column['COLUMN_NAME']] = $typeGenerator;

            $docBlockGenerator->setTag(new PropertyTag(Inflector::camelize($column['COLUMN_NAME']), $column['DATA_TYPE']));
        }

        $propertyGenerator->setDefaultValue($value, PropertyValueGenerator::TYPE_ARRAY);

        return $propertyGenerator;
    }

    protected function getBelongsToPropertyGenerator($table, $dbName, Connection $connection, DocBlockGenerator $docBlockGenerator)
    {
        $propertyGenerator = new PropertyGenerator('belongsTo');
        $value = [];

        $sql = "
            SELECT
                COLUMN_NAME,
                REFERENCED_TABLE_NAME
            FROM
                information_schema.KEY_COLUMN_USAGE
            WHERE
                CONSTRAINT_SCHEMA = '$dbName'
            AND TABLE_NAME = '$table'
            AND CONSTRAINT_NAME != 'PRIMARY'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ";

        $rows = $connection->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $name = preg_replace('/_id$/', '', $row['COLUMN_NAME']);
            $name = Inflector::camelize($name);
            $name = ucfirst($name);

            $modelName = Inflector::singularize($row['REFERENCED_TABLE_NAME']);
            $modelName = Inflector::camelize($modelName);
            $modelName = ucfirst($modelName);
            $modelName = 'Application\\Model\\' . $modelName;

            $value[$name] = [
                'model' => $modelName,
                'foreignKey' => $row['COLUMN_NAME']
            ];

            $docBlockGenerator->setTag(new PropertyTag($name, '\\' . $modelName));
            $docBlockGenerator->setTag(new MethodTag($name, '\\' . $modelName));
        }

        $propertyGenerator->setDefaultValue($value, PropertyValueGenerator::TYPE_ARRAY);

        return $propertyGenerator;
    }

    protected function getHasManyPropertyGenerator($table, $dbName, Connection $connection, DocBlockGenerator $docBlockGenerator)
    {
        $propertyGenerator = new PropertyGenerator('hasMany');
        $value = [];

        $sql = "
            SELECT
                COLUMN_NAME,
                TABLE_NAME,
                REFERENCED_TABLE_NAME
            FROM
                information_schema.KEY_COLUMN_USAGE
            WHERE
                CONSTRAINT_SCHEMA = '$dbName'
            AND REFERENCED_TABLE_NAME = '$table'
        ";

        $rows = $connection->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

        $countByTableName = [];
        foreach ($rows as $key => $row) {
            if (!isset($countByTableName[$row['TABLE_NAME']])) {
                $countByTableName[$row['TABLE_NAME']] = 0;
            }
            $countByTableName[$row['TABLE_NAME']]++;
        }

        foreach ($rows as $row) {
            $name = $row['TABLE_NAME'];
            if ($countByTableName[$row['TABLE_NAME']] > 1) {
               $name =  preg_replace('/_by_id$/', '', $row['COLUMN_NAME']) . '_' . $name;
            }

            $name = Inflector::camelize($name);
            $name = ucfirst($name);

            $modelName = Inflector::singularize($row['TABLE_NAME']);
            $modelName = Inflector::camelize($modelName);
            $modelName = ucfirst($modelName);
            $modelName = 'Application\\Model\\' . $modelName;

            $value[$name] = [
                'model' => $modelName,
                'foreignKey' => $row['COLUMN_NAME']
            ];

            $docBlockGenerator->setTag(new PropertyTag($name, '\\' . $modelName));
            $docBlockGenerator->setTag(new MethodTag($name, '\\' . $modelName));

            if ($table != $row['TABLE_NAME']) {
                # has many through
                $sql = "
                SELECT
                    COLUMN_NAME,
                    REFERENCED_TABLE_NAME
                FROM
                    information_schema.KEY_COLUMN_USAGE
                WHERE
                    CONSTRAINT_SCHEMA = '$dbName'
                AND TABLE_NAME = '{$row['TABLE_NAME']}'
                AND REFERENCED_TABLE_NAME != '$table'
            ";

                $throughRows = $connection->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

                foreach ($throughRows as $throughRow) {
                    $name = Inflector::camelize($throughRow['REFERENCED_TABLE_NAME']);
                    $name = ucfirst($name);
                    $modelName2 = 'Application\\Model\\' . Inflector::singularize($name);

                    if ($modelName2 == $modelName) {
                        continue;
                    }

                    $lefKey = $row['COLUMN_NAME'];
                    $rightKey = $throughRow['COLUMN_NAME'];

                    $value[$name] = [
                        'model' => $modelName2,
                        'through' => $modelName,
                        'leftKey' => $lefKey,
                        'rightKey' => $rightKey
                    ];

                    $docBlockGenerator->setTag(new PropertyTag($name, '\\' . $modelName2));
                    $docBlockGenerator->setTag(new MethodTag($name, '\\' . $modelName2));
                }
            }
        }

        $propertyGenerator->setDefaultValue($value, PropertyValueGenerator::TYPE_ARRAY);

        return $propertyGenerator;
    }
}