<?php

namespace Lazy\Session;

class Session
{
    protected $started;

    public function __construct()
    {
        $this->started = session_status() == 2;
        $this->start();
    }

    public function __invoke()
    {
        $args = func_get_args();

        switch (count($args)) {
            case 0: return $this;
            case 1: return $this->get($args[0]);
            default: return $this->set($args[0], $args[1]);
        }
    }

    public function id($id = null)
    {
        if (!func_num_args()) {
            return session_id();
        }

        session_id($id);
        return $this;
    }

    public function start($id = null)
    {
        if ($this->started) {
            return $this;
        }

        !$id || $this->id($id);
        session_start();
        $this->started = true;
        return $this;
    }

    public function saveHandler()
    {
        call_user_func_array('session_set_save_handler', func_get_args());
        return $this;
    }

    public function get($name, $default = null)
    {
        return isset($_SESSION[$name])? $_SESSION[$name] : $default;
    }

    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
        return $this;
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }
}