<?php

namespace Sloths\View\Helper;

use Sloths\Misc\UrlUtils;

class Assets
{
    /**
     * @var array
     */
    protected $groups = [];

    /**
     * @var string
     */
    protected $uses;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $versionParamName = '___';

    /**
     * @var bool
     */
    protected $version;

    /**
     * @return $this
     */
    public function __invoke()
    {
        $args = func_get_args();

        switch (func_num_args()) {
            case 1:
                $arg0 = $args[0];

                if (is_array($arg0)) {
                    return $this->fromArray($arg0);
                }

                return $this->getGroup($args[0]);

        }

        return $this;
    }

    /**
     * @param string $source
     * @return bool
     */
    public static function isExternalSource($source)
    {
        return '//' == substr($source, 0, 2) || 'http://' == substr($source, 0, 7) || 'https://' == substr($source, 0, 8);
    }

    /**
     * @param string $source
     * @return string
     */
    public function applyVersion($source)
    {
        if (!($version = $this->getVersion()) || static::isExternalSource($source)) {
            return $source;
        }

        return UrlUtils::appendParams($source, [$this->getVersionParamName() => $version]);
    }

    /**
     * @param string $source
     * @return string
     */
    public function prepareSource($source)
    {
        if (($baseUrl = $this->getBaseUrl()) && '/' !== $source[0] && !static::isExternalSource($source)) {
            $source = $baseUrl . '/' . $source;
        }

        return $this->applyVersion($source);
    }


    /**
     * @param array $groups
     * @return $this
     */
    public function fromArray(array $groups)
    {
        foreach ($groups as $name => $group) {
            if (!$group instanceof AssertGroup) {
                $options = $group;

                if (!is_array($options)) {
                    $options = ['sources' => $options];
                }

                if (!isset($options['sources'])) {
                    if (isset($options['extends'])) {
                        $options['sources'] = [];
                    } else {
                        $options['sources'] = $options;
                    }
                }

                $group = new AssertGroup($this);

                if (isset($options['extends'])) {
                    $group->extend($options['extends']);
                }

                $group->setSources($options['sources']);
            }

            $this->groups[$name] = $group;
        }

        return $this;
    }

    /**
     * @param string $baseUrl
     * @return $this
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setVersionParamName($name)
    {
        $this->versionParamName = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getVersionParamName()
    {
        return $this->versionParamName;
    }

    /**
     * @param $version
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $name
     * @param bool $strict
     * @return AssertGroup
     * @throws \RuntimeException
     */
    public function getGroup($name, $strict = true)
    {
        if (!isset($this->groups[$name]) && $strict) {
            throw new \RuntimeException('Group not found: ' . $name);
        }

        return $this->groups[$name];
    }

    /**
     * @param string $groupName
     * @return $this
     */
    public function uses($groupName)
    {
        $this->uses = $groupName;
        return $this;
    }

    /**
     * @param string $type
     * @return mixed
     */
    public function render($type = null)
    {
        return $this->getGroup($this->uses)->render($type);
    }
}

class AssertGroup
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
    protected $css  = [];

    /**
     * @var array
     */
    protected $js   = [];

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
        $parents = (array) $parents;
        $this->parents = array_merge($this->parents, $parents);
        return $this;
    }

    /**
     * @param string $sources
     * @return $this
     */
    public function setSources($sources)
    {
        $sources = (array) $sources;

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
        $type = $type?: (strtolower(pathinfo($source, PATHINFO_EXTENSION))?: 'js');

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