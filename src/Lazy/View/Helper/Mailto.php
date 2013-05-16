<?php

namespace Lazy\View\Helper;

class Mailto extends AbstractHelper
{
    public function mailto($email) {
        $email = $this->view->escape($email);
        return sprintf('<a href="mailto:%s">%s</a>', $email, $email);
    }
}