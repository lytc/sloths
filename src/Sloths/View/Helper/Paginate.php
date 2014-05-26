<?php

namespace Sloths\View\Helper;

use Sloths\Pagination\Paginator;

class Paginate extends AbstractHelper
{
    /**
     * @var Paginator
     */
    protected $paginator;

    /**
     * @var string
     */
    protected static $defaultTemplate;

    /**
     * @var string
     */
    protected $template;
    /**
     * @var string
     */
    protected $pageParamName = 'page';

    /**
     * @var string
     */
    protected $url;

    /**
     * @param string $file
     */
    public static function setDefaultTemplate($file)
    {
        static::$defaultTemplate = $file;
    }

    /**
     * @return mixed
     */
    public static function getDefaultTemplate()
    {
        if (!static::$defaultTemplate) {
            static::$defaultTemplate = __DIR__ . '/paginate/default.php';
        }
        return static::$defaultTemplate;
    }

    /**
     * @param string $file
     * @return $this
     */
    public function setTemplate($file)
    {
        $this->template = $file;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template?: static::getDefaultTemplate();
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setPageParamName($name)
    {
        $this->pageParamName = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getPageParamName()
    {
        return $this->pageParamName;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param int $pageNumber
     * @return mixed
     */
    public function url($pageNumber)
    {
        if ($this->url) {
            return $this->view->url($this->url, [$this->pageParamName => $pageNumber]);
        } else {
            return $this->view->url([$this->pageParamName => $pageNumber]);
        }
    }

    /**
     * @param Paginator $paginator
     * @return $this
     */
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

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}