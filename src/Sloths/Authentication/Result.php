<?php

namespace Sloths\Authentication;

class Result
{
    const SUCCESS = 1;
    const ERROR_FAILURE = 0;
    const ERROR_IDENTITY_NOT_FOUND = -1;
    const ERROR_CREDENTIAL_INVALID = -2;

    /**
     * @var array
     */
    protected $defaultErrorMessages = [
        self::ERROR_FAILURE => 'Failed',
        self::ERROR_IDENTITY_NOT_FOUND => 'Identity not found',
        self::ERROR_CREDENTIAL_INVALID => 'Credential invalid'
    ];

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var int
     */
    protected $code;

    /**
     * @var string
     */
    protected $message;

    /**
     * @param int $code
     * @param mixed $data
     * @param string $message
     * @throws \InvalidArgumentException
     */
    public function __construct($code, $data = null, $message = '')
    {
        if (!in_array($code, [self::SUCCESS, self::ERROR_FAILURE, self::ERROR_IDENTITY_NOT_FOUND, self::ERROR_CREDENTIAL_INVALID])) {
            throw new \InvalidArgumentException(
                sprintf('Result code must be 1, 0, -1 or -2, %s given', is_scalar($code)? $code : gettype($code))
            );
        }

        $this->code = $code;
        $this->data = $data;
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->code == self::SUCCESS;
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return $this->code != self::SUCCESS;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        if ($this->isError()) {
            return $this->message? $this->message : $this->defaultErrorMessages[$this->getCode()];
        }
    }
}