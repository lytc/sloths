<?php

namespace Sloths\Application\Service;

class View extends \Sloths\View\View implements ServiceInterface
{
    use ServiceTrait;

    /**
     * @return $this
     */
    public function boot()
    {
        $this->setDirectory($this->getApplication()->getPath('views'));
        return $this;
    }
}