<?php

namespace Lazy\View\Helper;


class Partial extends AbstractHelper
{
    public function partial($viewFile, $variables = [])
    {
        $view = clone $this->view;
        $view->layout(false);
        $view->variables($variables);
        $view->template($viewFile);

        return $view->render();
    }
}