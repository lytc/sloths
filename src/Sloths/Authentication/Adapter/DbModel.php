<?php

namespace Sloths\Authentication\Adapter;

use Sloths\Authentication\Result;

class DbModel extends AbstractDb
{

    /**
     * @var \Sloths\Db\Model\Model
     */
    protected $modelClassName;

    /**
     * @param \Sloths\Db\Model\Model $modelClassName
     * @param string $identityColumn
     * @param string $credentialColumn
     */
    public function __construct($modelClassName,
                                $identityColumn = self::DEFAULT_IDENTITY_COLUMN,
                                $credentialColumn = self::DEFAULT_CREDENTIAL_COLUMN)
    {
        $this->modelClassName = $modelClassName;
        $this->identityColumn = $identityColumn;
        $this->credentialColumn = $credentialColumn;
    }

    /**
     * @return \Sloths\Db\Model\Model
     */
    public function getModelClassName()
    {
        return $this->modelClassName;
    }

    /**
     * @return \Sloths\Authentication\Result
     */
    public function authenticate()
    {
        $modelClassName = $this->modelClassName;
        $model = $modelClassName::first($this->identityColumn, $this->identity);

        if (!$model) {
            $code = Result::ERROR_IDENTITY_NOT_FOUND;
        } else {
            if ($this->verifyCredential($model->{$this->credentialColumn})) {
                $code = Result::SUCCESS;
            } else {
                $code = Result::ERROR_CREDENTIAL_INVALID;
            }
        }

        $result = new Result($code, $model);
        return $result;
    }
}