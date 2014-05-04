<?php

namespace Application\View\Helper;

use Lazy\View\Helper\AbstractHelper;
use \Closure;

class Highlight extends AbstractHelper
{
    public function highlight($code)
    {
        if ($code instanceof Closure) {
            ob_start();
            call_user_func($code);
            $code = ob_get_clean();
        }

        $code = "<?php\n" . $code;

        return '<pre>' . highlight_string($code, true) . '</pre>';
    }
}