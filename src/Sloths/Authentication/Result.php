<?php

namespace Sloths\Authentication;

class Result
{
    const SUCCESS = 1;
    const ERROR_FAILURE = 0;
    const ERROR_IDENTITY_NOT_FOUND = -1;
    const ERROR_CREDENTIAL_INVALID = -2;

    /**
     * @var int
     */
    protected $code;

    /**
     * @var array
     */
    protected $messages = [
        self::SUCCESS => 'Success',
        self::ERROR_FAILURE => 'Failed',
        self::ERROR_IDENTITY_NOT_FOUND => 'Identity not found',
        self::ERROR_CREDENTIAL_INVALID => 'Credential is invalid'
    ];

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @param int $code
     * @param null $data
     * @param array $messages
     */
    public function __construct($code = null, $data = null, array $messages = [])
    {
        $this->setCode($code);
        $this->setData($data);

        if ($messages) {
            $this->setMessages($messages);
        }
    }

    /**
     * @param $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $messages
     * @return $this
     */
    public function setMessages(array $messages)
    {
        $this->messages = array_replace($this->messages, $messages);
        return $this;
    }

    /**
     * @return null
     */
    public function getMessage()
    {
        $code = $this->getCode();
        return array_key_exists($code, $this->messages)? $this->messages[$code] : null;
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
}