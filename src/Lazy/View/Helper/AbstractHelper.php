<?php

namespace Lazy\View\Helper;

use Lazy\View\View;

abstract class AbstractHelper
{
    protected $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }
}