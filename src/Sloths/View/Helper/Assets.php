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
            if (!$group instanceof Assets\Group) {
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

                $group = new Assets\Group($this);

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
     * @return \Sloths\View\Helper\Assets\Group
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
