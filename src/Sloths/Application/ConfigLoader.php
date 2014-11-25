<?php

namespace Sloths\Application;

use Sloths\Misc\ConfigurableInterface;

class ConfigLoader
{
    /**
     * @var array
     */
    protected $directories = [];

    /**
     * @var ApplicationInterface
     */
    protected $application;

    /**
     * @var array
     */
    protected $map;

    /**
     * @param ApplicationInterface $application
     */
    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * @param string|array $directories
     * @return $this
     */
    public function addDirectories($directories)
    {
        if (!is_array($directories)) {
            $directories = [$directories];
        }

        foreach ($directories as $directory) {
            $directory = realpath($directory);
            $this->directories[$directory] = $directory;
        }

        return $this;
    }

    /**
     * @param string $directory
     * @return array
     */
    protected function map($directory)
    {
        $map = [];

        if (is_dir($directory)) {
            $dir = dir($directory);
            while (false !== ($f = $dir->read())) {
                if ('.' == $f || '..' == $f || is_dir($f)) {
                    continue;
                }

                $map[pathinfo($f, PATHINFO_FILENAME)] = $directory . '/' . $f;
            }

            $dir->close();
        }

        return $map;
    }

    /**
     *
     */
    public function initialize()
    {
        if (null === $this->map) {
            $map = [];

            # global
            foreach ($this->directories as $directory) {
                $map = array_merge_recursive($map, $this->map($directory . '/global'));
            }

            # local
            $env = $this->application->getEnv();

            foreach ($this->directories as $directory) {
                $map = array_merge_recursive($map, $this->map($directory . '/local/' . $env));
            }

            $this->map = $map;
        }

        return $this;
    }

    /**
     * @param string $name
     * @param ConfigurableInterface $object
     * @return $this
     */
    public function apply($name, ConfigurableInterface $object)
    {
        $this->initialize();

        if (isset($this->map[$name])) {
            $files = $this->map[$name];

            $object->loadConfigFromFile($files);
        }

        return $this;
    }
}