<?php

namespace Lazy\View\Helper;

use Lazy\Pagination\Paginator;
use Lazy\Util\UrlUtils;

class Paginate extends AbstractHelper
{
    protected $paginator;
    protected static $defaultTemplate;
    protected $template;
    protected $pageParamName = 'page';
    protected static $requestUrl;

    public static function setDefaultTemplate($file)
    {
        static::$defaultTemplate = $file;
    }

    public static function getDefaultTemplate()
    {
        if (!static::$defaultTemplate) {
            static::$defaultTemplate = __DIR__ . '/paginate/default.php';
        }
        return static::$defaultTemplate;
    }

    public static function setRequestUrl($requestUrl)
    {
        static::$requestUrl = $requestUrl;
    }

    public function setTemplate($file)
    {
        $this->template = $file;
        return $this;
    }

    public function getTemplate()
    {
        return $this->template?: static::getDefaultTemplate();
    }

    public function setPageParamName($name)
    {
        $this->pageParamName = $name;
        return $this;
    }

    public function getPageParamName()
    {
        return $this->pageParamName;
    }

    public function url($pageNumber)
    {
        return $this->view->url([$this->pageParamName => $pageNumber]);
    }

    public function paginate(Paginator $paginator)
    {
        $this->paginator = $paginator;
        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        $template = $this->getTemplate();
        return $this->view->partial($template, ['paginator' => $this->paginator, 'paginate' => $this])->render();
    }

    public function __toString()
    {
        return $this->render();
    }
}