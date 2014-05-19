<?php

namespace Sloths\View\Helper;

class Partial extends AbstractHelper
{
    public function partial($file, $vars = [])
    {
        $view = clone $this->view;
        $view->setLayout(false)->setFile($file);

        if ($vars) {
            $view->addVars($vars);
        }

        return $view;
    }
}