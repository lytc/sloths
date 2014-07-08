<?php

namespace Sloths\Session;

class Messages implements \Countable, \IteratorAggregate
{
    const SUCCESS   = 'success';
    const INFO      = 'info';
    const WARNING   = 'warning';
    const ERROR     = 'error';

    /**
     * @var Flash
     */
    protected $messages;

    /**
     * @param string $flashSession
     */
    public function __construct($flashSession = '__SLOTHS_FLASH_MESSAGE__')
    {
        if (!$flashSession instanceof Flash) {
            $flashSession = new Flash($flashSession);
        }

        $this->messages = $flashSession;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->messages);
    }

    /**
     * @return Flash
     */
    public function getIterator()
    {
        return $this->messages;
    }

    /**
     * @return Flash
     */
    public function getFlashSession()
    {
        return $this->messages;
    }

    /**
     * @return $this
     */
    public function now()
    {
        $this->messages->now();
        return $this;
    }

    /**
     * @return $this
     */
    public function keep()
    {
        $this->messages->keep();
        return $this;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->messages->clear();
        return $this;
    }

    /**
     * @param string $type
     * @param mixed $message
     * @return $this
     */
    public function add($type, $message)
    {
        $this->messages[] = [
            'type' => $type,
            'message' => $message
        ];

        return $this;
    }

    /**
     * @param mixed $message
     * @return $this
     */
    public function success($message)
    {
        return $this->add(static::SUCCESS, $message);
    }

    /**
     * @param mixed $message
     * @return $this
     */
    public function info($message)
    {
        return $this->add(static::INFO, $message);
    }

    /**
     * @param mixed $message
     * @return $this
     */
    public function warning($message)
    {
        return $this->add(static::WARNING, $message);
    }

    /**
     * @param mixed $message
     * @return $this
     */
    public function error($message)
    {
        return $this->add(static::ERROR, $message);
    }
}