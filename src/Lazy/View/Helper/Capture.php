<?php

namespace Lazy\View\Helper;

class Capture extends AbstractHelper
{
    protected static $instances = [];
    protected $parts = [];

    public function capture($name, $items = null) {
        if (!isset($this->parts[$name])) {
            $this->parts[$name] = new CapturePart();
        }
        if (func_num_args() == 1) {
            return $this->parts[$name];
        }

        if ($items instanceof \Closure) {
            ob_start();
            $items();
            $items = ob_get_clean();
        }

        $this->parts[$name]->append($items);

        return $this;
    }
}

class CapturePart
{
    protected $items = [];

    public function append($items)
    {
        if (!is_array($items)) {
            $items = [$items];
        }

        $this->items = array_merge($this->items, $items);
        return $this;
    }

    public function prepend($items)
    {
        if (!is_array($items)) {
            $items = [$items];
        }

        $this->items = array_merge($items, $this->items);
        return $this;
    }

    public function __toString()
    {
        return implode('', $this->items);
    }
}