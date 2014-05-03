<?php

namespace Lazy\View\Helper;

use Lazy\View\View;

abstract class AbstractHelper
{
    /**
     * @var \Lazy\View\View
     */
    protected $view;

    /**
     * @param View $view
     */
    public function __construct(View $view)
    {
        $this->view = $view;
    }
}