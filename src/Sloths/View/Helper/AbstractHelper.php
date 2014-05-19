<?php

namespace Sloths\View\Helper;

use Sloths\View\View;

abstract class AbstractHelper
{
    /**
     * @var \Sloths\View\View
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