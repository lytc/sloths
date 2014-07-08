<?php

namespace Sloths\View\Helper;

class Tag extends AbstractHelper
{
    /**
     * @var string
     */
    protected $tagName;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var array
     */
    protected $defaultAttributes = [];

    /**
     * @var string|array|Tag
     */
    protected $children = [];

    /**
     * @param string $tagName
     * @param array [$attributes]
     * @return $this
     */
    public function tag($tagName, array $attributes = null) {
        $this->tagName = $tagName;
        !$attributes || $this->attributes = $attributes;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasAttribute($name)
    {
        return isset($this->attributes[$name]);
    }

    /**
     * @param string $name
     * @return null
     */
    public function getAttribute($name)
    {
        return $this->hasAttribute($name)? $this->attributes[$name] : null;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function addAttributes(array $attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function removeAttribute($name)
    {
        if (array_key_exists($name, $this->attributes)) {
            unset($this->attributes[$name]);
        }

        return $this;
    }

    /**
     * @param string|array|mixed $children
     * @return $this
     */
    public function setChildren($children)
    {
        if (!is_array($children)) {
            $children = [$children];
        }
        $this->children = $children;
        return $this;
    }

    /**
     * @return array|Tag|string
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param string|array|mixed $children
     * @return $this
     */
    public function addChildren($children)
    {
        if (!is_array($children)) {
            $children = [$children];
        }
        $this->children = array_merge($this->children, $children);
        return $this;
    }

    /**
     * @param string $str
     * @return string
     */
    protected static function escape($str)
    {
        return htmlspecialchars($str);
    }

    /**
     * @return array
     */
    protected function buildAttributes()
    {
        $attributes = array_merge($this->defaultAttributes, $this->attributes);

        $attrs = [];
        foreach ($attributes as $key => $value) {
            $attrs[] = sprintf('%s="%s"', self::escape($key), self::escape($value));
        }

        return $attrs;
    }

    /**
     * @return string
     */
    protected function getPattern()
    {
        $pattern = '<%s%s>';
        if (!in_array($this->tagName, ['area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen',
            'link', 'meta', 'param', 'source', 'track', 'wbr'])) {
            $pattern .= '%s</%s>';
        }

        return $pattern;
    }

    /**
     * @return string
     */
    public function render()
    {
        $attributes = $this->buildAttributes();
        $children = implode('', $this->children);
        $pattern = $this->getPattern();

        return sprintf($pattern,
            $this->tagName,
            $attributes? ' ' . implode(' ', $attributes) : '',
            $children,
            $this->tagName);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();

    }
}