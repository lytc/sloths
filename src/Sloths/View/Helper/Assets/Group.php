<?php

namespace Sloths\View\Helper\Assets;

use Sloths\View\Helper\Assets;

class Group
{
    /**
     * @var Assets
     */
    protected $assets;

    /**
     * @var array
     */
    protected $parents = [];

    /**
     * @var array
     */
    protected $css = [];

    /**
     * @var array
     */
    protected $js = [];

    /**
     * @param Assets $assets
     */
    public function __construct(Assets $assets)
    {
        $this->assets = $assets;
    }

    /**
     * @param string|array $parents
     * @return $this
     */
    public function extend($parents)
    {
        $parents = (array)$parents;
        $this->parents = array_merge($this->parents, $parents);
        return $this;
    }

    /**
     * @param string $sources
     * @return $this
     */
    public function setSources($sources)
    {
        $sources = (array)$sources;

        foreach ($sources as $source => $type) {
            if (is_numeric($source)) {
                $source = $type;
                $type = null;
            }

            $this->addSource($source, $type);
        }

        return $this;
    }

    /**
     * @param string $source
     * @param string $type
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function addSource($source, $type = null)
    {
        if (!$type) {
            $type = strtolower(pathinfo(parse_url($source, PHP_URL_PATH), PATHINFO_EXTENSION));
        }

        if ($type == 'js') {
            $this->addJs($source);
        } elseif ($type == 'css') {
            $this->addCss($source);
        } else {
            throw new \InvalidArgumentException(sprintf('Asset type must be js or css, %s given', $type));
        }

        return $this;
    }

    /**
     * @param string $source
     * @return $this
     */
    public function addCss($source)
    {
        $this->css[$source] = $source;
        return $this;
    }

    /**
     * @param string $source
     * @return $this
     */
    public function addJs($source)
    {
        $this->js[$source] = $source;
        return $this;
    }

    public function getCss()
    {
        $sources = [];

        foreach ($this->parents as $parentGroupName) {
            $sources = array_replace($sources, $this->assets->getGroup($parentGroupName)->getCss());
        }

        $sources = array_replace($sources, $this->css);

        foreach ($sources as &$source) {
            $source = $this->assets->prepareSource($source);
        }

        return $sources;
    }

    public function getJs()
    {
        $sources = [];

        foreach ($this->parents as $parentGroupName) {
            $sources = array_replace($sources, $this->assets->getGroup($parentGroupName)->getJs());
        }

        $sources = array_replace($sources, $this->js);

        foreach ($sources as &$source) {
            $source = $this->assets->prepareSource($source);
        }

        return $sources;
    }

    /**
     * @return string
     */
    public function renderCss()
    {
        $tags = [];

        foreach ($this->getCss() as $source) {
            $tags[] = '<link href="' . $source . '" rel="stylesheet" />';
        }

        return implode(PHP_EOL, $tags);
    }

    /**
     * @return string
     */
    public function renderJs()
    {
        $sources = [];
        $tags = [];
        foreach ($this->getJs() as $source) {
            $tags[] = '<script src="' . $source . '"></script>';
        }

        return implode(PHP_EOL, $tags);
    }

    /**
     * @param string $type
     * @return string
     * @throws \InvalidArgumentException
     */
    public function render($type = null)
    {
        if ($type && $type != 'css' && $type != 'js') {
            throw new \InvalidArgumentException(sprintf('Type must be js or css, %s given', $type));
        }

        $result = [];

        if (!$type || $type == 'css') {
            $result[] = $this->renderCss();
        }

        if (!$type || $type == 'js') {
            $result[] = $this->renderJs();
        }

        return implode(PHP_EOL, $result);
    }
}