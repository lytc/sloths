<?php

namespace Lazy\View\Helper;

use Lazy\Pagination\Pagination;

class Paginate extends AbstractHelper
{
    protected $pagination;
    protected static $defaultTemplate = 'paginate';
    protected $template;

    public static function setDefaultTemplate($template)
    {
        static::$defaultTemplate = $template;
    }

    public function paginate(Pagination $pagination, $template = null)
    {
        $this->pagination = $pagination;
        !$template || $this->template($template);
        return $this;
    }

    public function template($template = null)
    {
        if (!func_num_args()) {
            return $this->template?: static::$defaultTemplate;
        }

        $this->template = $template;
        return $this;
    }

    public function __toString()
    {
        try {
            $view = clone $this->view;

            $result = $view->layout(false)
                ->variables($this->pagination->info())
                ->render($this->template());
        } catch (\Exception $e) {
            return $e->getMessage();
        }


        return $result;
    }
}