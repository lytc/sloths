<?php

namespace Lazy\Session;

class Flash
{
    const PARAM_NAME = '__lite.flash__';
    protected $currentData = [];
    protected $nextData = [];

    public function __construct()
    {
        $this->currentData = isset($_SESSION[self::PARAM_NAME])? $_SESSION[self::PARAM_NAME] : [];
    }

    public function __invoke()
    {
        if (!func_num_args()) {
            return $this;
        }

        return call_user_func_array([$this, 'data'], func_get_args());
    }

    public function data($name = null, $value = null)
    {
        switch (func_num_args()) {
            case 0: return $this->currentData;
            case 1:
                if (is_array($name)) {
                    $this->nextData = array_merge($this->nextData, $name);
                    return $this;
                }

                return isset($this->currentData[$name])? $this->currentData[$name] : null;

            default:
                $this->nextData[$name] = $value;
                return $this;
        }
    }

    public function __destruct()
    {
        unset($_SESSION[self::PARAM_NAME]);
        $_SESSION[self::PARAM_NAME] = $this->nextData;
    }
}