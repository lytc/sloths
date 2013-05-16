<?php

namespace Lazy\View\Helper;

class Capture extends AbstractHelper
{
    protected static $instances = [];
    protected $data = [];

    public function capture($name, $content = null) {
        if (func_num_args() == 1) {
            return isset($this->data[$name])? $this->data[$name] : '';
        }

        if (is_array($content)) {
            $content = implode('', $content);
        } elseif ($content instanceof \Closure) {
            ob_start();
            $content();
            $content = ob_get_clean();
        }


        isset($this->data[$name]) || ($this->data[$name] = '');
        $this->data[$name] .= $content;
    }
}