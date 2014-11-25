<?php

namespace Sloths\View\Helper;

use Sloths\View\View;

trait HelperTrait
{
    /**
     * @var
     */
    protected $view;

    /**
     * @param View $view
     * @return $this
     */
    public function setView(View $view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * @param bool $strict
     * @return View
     * @throws \DomainException
     */
    public function getView($strict = true)
    {
        if ($this->view) {
            return $this->view;
        }

        if ($strict) {
            throw new \DomainException('View is required');
        }
    }
}