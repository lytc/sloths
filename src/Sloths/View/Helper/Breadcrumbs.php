<?php

namespace Sloths\View\Helper;

class Breadcrumbs extends AbstractHelper
{
    /**
     * @var string
     */
    protected $template;

    /**
     * @var array
     */
    protected $elements = [];

    public function __invoke(array $elements = null)
    {
        if ($elements) {
            $this->setElements($elements);
        }

        return $this;
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        if (!$this->template) {
            $this->setTemplate(__DIR__ . '/resources/breadcrumbs.html.php');
        }

        return $this->template;
    }

    /**
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * @param array $elements
     * @return $this
     */
    public function setElements(array $elements)
    {
        foreach ($elements as $label => $link) {
            if (!$label) {
                $label = $link;
                $link = null;
            }

            $this->add($label, $link);
        }
        return $this;
    }

    /**
     * @param string $label
     * @param string $link
     * @return $this
     */
    public function add($label, $link = null)
    {
        $this->elements[] = ['label' => $label, 'link' => $link];

        return $this;
    }

    public function render()
    {
        $template = $this->getTemplate();

        return $this->getView()->partial($template, ['elements' => $this->getElements()]);
    }
}