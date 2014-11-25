<?php

namespace Sloths\Db\Model\Relation;

class HasManyThroughSchema extends AbstractSchema
{
    /**
     * @var string
     */
    protected $selfModelClassName;

    /**
     * @var string
     */
    protected $throughModelClassName;

    /**
     * @var \Sloths\Db\Model\AbstractModel
     */
    protected $throughModel;

    /**
     * @var string
     */
    protected $leftForeignKey;

    /**
     * @var string
     */
    protected $rightForeignKey;

    /**
     * @param string $selfModelClassName
     * @param string $modelClassName
     * @param string $throughModelClassName
     */
    public function __construct($selfModelClassName, $modelClassName, $throughModelClassName)
    {
        $this->selfModelClassName       = $selfModelClassName;
        $this->modelClassName           = $modelClassName;
        $this->throughModelClassName    = $throughModelClassName;
    }

    /**
     * @return string
     */
    public function getThroughModelClassName()
    {
        return $this->throughModelClassName;
    }

    /**
     * @return \Sloths\Db\Model\AbstractModel
     */
    public function getThroughModel()
    {
        if (!$this->throughModel) {
            $throughModelClassName = $this->getThroughModelClassName();
            $this->throughModel = new $throughModelClassName();
        }

        return $this->throughModel;
    }

    /**
     *
     */
    protected function processForeignKey()
    {
        if (!$this->leftForeignKey) {
            $allBelongsToSchema = $this->getThroughModel()->getAllBelongsToSchema();

            foreach ($allBelongsToSchema as $belongsToSchema) {
                if ($belongsToSchema->getModelClassName() == $this->selfModelClassName) {
                    $this->leftForeignKey = $belongsToSchema->getForeignKey();
                    continue;
                }

                if ($belongsToSchema->getModelClassName() == $this->getModelClassName()) {
                    $this->rightForeignKey = $belongsToSchema->getForeignKey();
                    continue;
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getLeftForeignKey()
    {
        $this->processForeignKey();
        return $this->leftForeignKey;
    }

    /**
     * @return string
     */
    public function getRightForeignKey()
    {
        $this->processForeignKey();
        return $this->rightForeignKey;
    }
}