<?php

namespace Sloths\View;

class Capture
{
    /**
     * @var string
     */
    protected $items = [];

    /**
     * @var callable
     */
    protected $renderer;

    /**
     * @param mixed $items
     * @return $this
     */
    public function append($items)
    {
        is_array($items) || ($items = [$items]);
        $this->items = array_merge($this->items, array_values($items));
        return $this;
    }

    /**
     * @param mixed $items
     * @return $this
     */
    public function prepend($items)
    {
        is_array($items) || ($items = [$items]);
        $this->items = array_merge(array_values($items), $this->items);
        return $this;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->items = [];
        return $this;
    }

    /**
     * @param callable $renderer
     * @return $this
     */
    public function setRenderer(callable $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }

    /**
     * @return \Closure
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @param callable $renderer
     * @return string
     */
    public function render(callable $renderer = null)
    {
        !$renderer || $this->setRenderer($renderer);
        $items = $result = $this->items;
        if ($renderer = $this->renderer) {
            $result = [];
            foreach ($items as $item) {
                $result[] = '' . call_user_func($renderer, $item);
            }
        }

        return implode('', $result);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}