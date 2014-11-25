<?php

namespace Sloths\View\Helper;

class Partial extends AbstractHelper
{
    /**
     * @param $template
     * @param array $variables
     * @return string
     */
    public function __invoke($template, array $variables = [])
    {
        $view = clone $this->getView();
        $view->setLayout(false);

        return $view->render($template, $variables);
    }
}