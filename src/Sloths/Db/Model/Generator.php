<?php

namespace Sloths\Db\Model;

use Sloths\Db\Connection;
use Sloths\Db\Schema\Table;
use Sloths\Util\Inflector;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlock\Tag\PropertyTag;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\PropertyGenerator;
use Zend\Code\Generator\ValueGenerator;
use Zend\Code\Reflection\ClassReflection;

class Generator
{
    /**
     * @var string
     */
    protected $directory;

    /**
     * @var \Zend\Code\Generator\ClassGenerator
     */
    protected $classGenerator;

    /**
     * @var string
     */
    protected $abstractModelClassName;

    /**
     * @param string $tableName
     * @param string $namespaceName
     * @param Connection $connection
     * @return static
     */
    public static function fromTable($tableName, $namespaceName, Connection $connection)
    {
        $className = Inflector::classify(Inflector::singularize($tableName));
        if ($namespaceName) {
            $className = $namespaceName . '\\' . $className;
        }

        $generator = new static($className);

        $tableSchema = Table::fromCache($tableName, $connection);

        # primary key
        $generator->setPrimaryKey($tableSchema->getPrimaryKeyColumn());

        # table name
        $generator->setTableName($tableName);

        # $columns
        $columnSchemas = $tableSchema->getColumns();

        $columns = [];
        foreach ($columnSchemas as $name => $meta) {
            $columns[$name] = $meta['type'];
        }

        $generator->setColumns($columns);

        # $hasMany
        $belongsToConstraints = $tableSchema->getHasManyConstraints();
        $hasMany = [];
        foreach ($belongsToConstraints as $tableName => $meta) {
            $constraintName = Inflector::classify(Inflector::pluralize($tableName));
            $hasMany[$constraintName] = [
                'model' => '\\' . $generator->classGenerator->getNamespaceName() . '\\' . Inflector::classify(Inflector::singularize($tableName)),
                'foreignKey' => $meta['foreignKey']
            ];
        }

        !$hasMany || $generator->setHasMany($hasMany);

        # $hasOne
        $belongsToConstraints = $tableSchema->getHasOneConstraints();
        $hasOne = [];
        foreach ($belongsToConstraints as $tableName => $meta) {
            $constraintName = Inflector::classify(Inflector::singularize($tableName));
            $hasOne[$constraintName] = [
                'model' => '\\' . $generator->classGenerator->getNamespaceName() . '\\' . Inflector::classify(Inflector::singularize($tableName)),
                'foreignKey' => $meta['foreignKey']
            ];
        }

        !$hasOne || $generator->setHasOne($hasOne);

        # $belongsTo
        $belongsToConstraints = $tableSchema->getBelongsToConstraints();
        $belongsTo = [];
        foreach ($belongsToConstraints as $columnName => $meta) {
            $constraintName = Inflector::classify(Inflector::singularize(preg_replace('/_id$/', '', $columnName)));
            $belongsTo[$constraintName] = [
                'model' => '\\' . $generator->classGenerator->getNamespaceName() . '\\' . Inflector::classify(Inflector::singularize($meta['table'])),
                'foreignKey' => $meta['foreignKey']
            ];
        }

        !$belongsTo || $generator->setBelongsTo($belongsTo);

        # $hasManyThrough
        $hasManyThroughConstraints = $tableSchema->getHasManyThroughConstraints();
        $hasManyThrough = [];
        foreach ($hasManyThroughConstraints as $meta) {
            $constraintName = $meta['tableName'];
            if (isset($meta['hasBelongsTo'])) {
                $constraintName = Inflector::singularize($meta['throughTableName']) . '_' . $constraintName;
            }
            $constraintName = Inflector::classify(Inflector::pluralize($constraintName));
            $hasManyThrough[$constraintName] = [
                'through' => [
                    'model' => '\\' . $generator->classGenerator->getNamespaceName() . '\\' . Inflector::classify(Inflector::singularize($meta['throughTableName'])),
                    'leftKey' => $meta['leftKey'],
                    'rightKey' => $meta['rightKey'],
                ],
                'model' => '\\' . $generator->classGenerator->getNamespaceName() . '\\' . Inflector::classify(Inflector::singularize($meta['tableName'])),
            ];
        }

        !$hasManyThrough || $generator->setHasManyThrough($hasManyThrough);

        return $generator;
    }

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        if (class_exists($name)) {
            $this->classGenerator = ClassGenerator::fromReflection(new ClassReflection($name));
        } else {
            $this->classGenerator = new ClassGenerator($name);
        }

        $dockBlockGenerator = $this->classGenerator->getDocBlock();

        if (!$dockBlockGenerator) {
            $dockBlockGenerator = new CustomDockBlockGenerator();
        }

        if (!$dockBlockGenerator instanceof CustomDockBlockGenerator) {
            $dockBlockGenerator = new CustomDockBlockGenerator(
                $dockBlockGenerator->getShortDescription(),
                $dockBlockGenerator->getLongDescription(),
                $dockBlockGenerator->getTags()
            );
        }
        $dockBlockGenerator->setSourceDirty(true);
        $this->classGenerator->setDocBlock($dockBlockGenerator);
    }

    /**
     * @return ClassGenerator
     */
    public function getClassGenerator()
    {
        return $this->classGenerator;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setAbstractModelClassName($name)
    {
        $this->abstractModelClassName = $name;
        return $this;
    }

    /**
     * @param string $directory
     * @return $this
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
        return $this;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory . '/' . str_replace('\\', '/', $this->classGenerator->getNamespaceName());
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->getDirectory() . '/' . $this->classGenerator->getName() . '.php';
    }

    /**
     * @param string $columnName
     */
    public function setPrimaryKey($columnName)
    {
        $propertyGenerator = $this->classGenerator->getProperty('primaryKey');

        if ($propertyGenerator) {
            $propertyGenerator->setDefaultValue($columnName);
        } else {
            $propertyGenerator = new PropertyGenerator('primaryKey', $columnName);
            $propertyGenerator->setVisibility(PropertyGenerator::VISIBILITY_PROTECTED)->setStatic(true);
            $this->classGenerator->addPropertyFromGenerator($propertyGenerator);
        }
    }

    /**
     * @param string $tableName
     */
    public function setTableName($tableName)
    {
        $propertyGenerator = $this->classGenerator->getProperty('tableName');

        if ($propertyGenerator) {
            $propertyGenerator->setDefaultValue($tableName);
        } else {
            $propertyGenerator = new PropertyGenerator('tableName', $tableName);
            $propertyGenerator->setVisibility(PropertyGenerator::VISIBILITY_PROTECTED)->setStatic(true);
            $this->classGenerator->addPropertyFromGenerator($propertyGenerator);
        }
    }

    /**
     * @param array $columns
     */
    public function setColumns(array $columns)
    {
        foreach ($columns as $name => $type) {
            $type = strtoupper($type);
            $this->classGenerator->getDocBlock()->setTag(new PropertyTag(Inflector::camelize($name), [constant('\Sloths\Db\Model\Model::' . $type)]));
            $columns[$name] = new ValueGenerator(sprintf('self::%s', $type), ValueGenerator::TYPE_CONSTANT);
        }

        $propertyGenerator = $this->classGenerator->getProperty('columns');

        if ($propertyGenerator) {
            $propertyGenerator->setDefaultValue($columns);
        } else {
            $propertyGenerator = new PropertyGenerator('columns', $columns);
            $propertyGenerator->setVisibility(PropertyGenerator::VISIBILITY_PROTECTED)->setStatic(true);
            $this->classGenerator->addPropertyFromGenerator($propertyGenerator);
        }
    }

    /**
     * @param array $hasMany
     */
    public function setHasMany(array $hasMany)
    {
        foreach ($hasMany as $constraintName => $meta) {
            $this->classGenerator->getDocBlock()->setTag(new PropertyTag($constraintName, $meta['model']));
        }

        $propertyGenerator = $this->classGenerator->getProperty('hasMany');

        if ($propertyGenerator) {
            $value = array_replace($hasMany, $propertyGenerator->getDefaultValue()->getValue());
            $propertyGenerator->setDefaultValue($value);
        } else {
            $propertyGenerator = new PropertyGenerator('hasMany', $hasMany);
            $propertyGenerator->setVisibility(PropertyGenerator::VISIBILITY_PROTECTED)->setStatic(true);

            $this->classGenerator->addPropertyFromGenerator($propertyGenerator);
        }
    }

    /**
     * @param array $hasOne
     */
    public function setHasOne(array $hasOne)
    {
        foreach ($hasOne as $constraintName => $meta) {
            $this->classGenerator->getDocBlock()->setTag(new PropertyTag($constraintName, $meta['model']));
        }

        $propertyGenerator = $this->classGenerator->getProperty('hasOne');

        if ($propertyGenerator) {
            $value = array_replace($hasOne, $propertyGenerator->getDefaultValue()->getValue());
            $propertyGenerator->setDefaultValue($value);
        } else {
            $propertyGenerator = new PropertyGenerator('hasOne', $hasOne);
            $propertyGenerator->setVisibility(PropertyGenerator::VISIBILITY_PROTECTED)->setStatic(true);

            $this->classGenerator->addPropertyFromGenerator($propertyGenerator);
        }
    }

    /**
     * @param array $belongsTo
     */
    public function setBelongsTo(array $belongsTo)
    {
        foreach ($belongsTo as $constraintName => $meta) {
            $this->classGenerator->getDocBlock()->setTag(new PropertyTag($constraintName, $meta['model']));
        }

        $propertyGenerator = $this->classGenerator->getProperty('belongsTo');

        if ($propertyGenerator) {
            $value = array_replace($belongsTo, $propertyGenerator->getDefaultValue()->getValue());
            $propertyGenerator->setDefaultValue($value);
        } else {
            $propertyGenerator = new PropertyGenerator('belongsTo', $belongsTo);
            $propertyGenerator->setVisibility(PropertyGenerator::VISIBILITY_PROTECTED)->setStatic(true);

            $this->classGenerator->addPropertyFromGenerator($propertyGenerator);
        }
    }

    /**
     * @param array $hasManyThrough
     */
    public function setHasManyThrough(array $hasManyThrough)
    {
        foreach ($hasManyThrough as $constraintName => $meta) {
            $this->classGenerator->getDocBlock()->setTag(new PropertyTag($constraintName, $meta['model']));
        }

        $propertyGenerator = $this->classGenerator->getProperty('hasManyThrough');

        if ($propertyGenerator) {
            $value = array_replace($hasManyThrough, $propertyGenerator->getDefaultValue()->getValue());
            $propertyGenerator->setDefaultValue($value);
        } else {
            $propertyGenerator = new PropertyGenerator('hasManyThrough', $hasManyThrough);
            $propertyGenerator->setVisibility(PropertyGenerator::VISIBILITY_PROTECTED)->setStatic(true);

            $this->classGenerator->addPropertyFromGenerator($propertyGenerator);
        }
    }

    public function getAbstractModelFileName()
    {
        if ($this->abstractModelClassName) {
            return $this->getDirectory() . '/' . $this->abstractModelClassName . '.php';
        }
    }

    /**
     *
     */
    protected function writeAbstractModel()
    {
        $filename = $this->getAbstractModelFileName();

        if (file_exists($filename)) {
            return;
        }

        $classGenerator = new ClassGenerator();
        $classGenerator->setName($this->abstractModelClassName)
            ->setNamespaceName($this->classGenerator->getNamespaceName())
            ->setExtendedClass('\Sloths\Db\Model\Model');

        $fileGenerator = new FileGenerator();
        $fileGenerator->setClass($classGenerator)->setFilename($filename)->write();
    }

    /**
     * @return $this
     */
    public function write()
    {
        $filename = $this->getFilename();

        $dir = dirname($filename);
        file_exists($dir) || mkdir($dir, 0777, true);

        if ($this->abstractModelClassName) {
            $this->writeAbstractModel();
            $this->classGenerator->setExtendedClass($this->abstractModelClassName);
        } else {
            $this->classGenerator->setExtendedClass('\Sloths\Db\Model\Model');
        }


        $fileGenerator = new FileGenerator();
        $fileGenerator->setFilename($filename)->setClass($this->classGenerator);
        $fileGenerator->write();

        return $this;
    }
}

class CustomDockBlockGenerator extends DocBlockGenerator
{
    /**
     * @param array $tags
     * @param bool $replace
     * @return $this
     */
    public function setTags(array $tags, $replace = true)
    {
        foreach ($tags as $tag) {
            $this->setTag($tag, $replace);
        }

        return $this;
    }

    /**
     * @param array|\Zend\Code\Generator\DocBlock\Tag\TagInterface $tag
     * @param bool $replace
     * @return $this|DocBlockGenerator
     */
    public function setTag($tag, $replace = true)
    {
        if (!$tag instanceof PropertyTag) {
            return parent::setTag($tag);
        }

        $existingTag = $this->getPropertyTag($tag->getPropertyName());
        if ($existingTag) {
            $existingTag->setTypes($tag->getTypes());
        } else {
            return parent::setTag($tag);
        }

        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getPropertyTag($name)
    {
        foreach ($this->tags as $tag) {
            if ($tag instanceof PropertyTag && $tag->getPropertyName() == $name) {
                return $tag;
            }
        }
    }
}