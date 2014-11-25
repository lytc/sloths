<?php

namespace Sloths\View\Helper;

use Sloths\View\View;

interface HelperInterface
{
    /**
     * @param View $view
     * @return $this
     */
    public function setView(View $view);

    /**
     * @return View
     */
    public function getView();
}