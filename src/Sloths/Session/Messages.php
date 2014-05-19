<?php

namespace Sloths\Session;

class Messages implements \Countable, \IteratorAggregate
{
    const SUCCESS   = 'success';
    const INFO      = 'info';
    const WARNING   = 'warning';
    const ERROR     = 'error';

    protected $messages;

    public function __construct($flashSession = '__LAZY_FLASH_MESSAGE__')
    {
        if (!$flashSession instanceof Flash) {
            $flashSession = new Flash($flashSession);
        }

        $this->messages = $flashSession;
    }

    public function count()
    {
        return count($this->messages);
    }

    public function getIterator()
    {
        return $this->messages;
    }

    public function add($type, $message)
    {
        $this->messages[] = [
            'type' => $type,
            'message' => $message
        ];

        return $type;
    }

    public function success($message)
    {
        return $this->add(static::SUCCESS, $message);
    }

    public function info($message)
    {
        return $this->add(static::INFO, $message);
    }

    public function warning($message)
    {
        return $this->add(static::WARNING, $message);
    }

    public function error($message)
    {
        return $this->add(static::ERROR, $message);
    }
}