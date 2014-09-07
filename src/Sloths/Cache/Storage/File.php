<?php

namespace Sloths\Cache\Storage;

use Sloths\Filesystem\Filesystem;

class File implements StorageInterface
{
    const METADATA_FILE_NAME = 'metadata.json';
    const STORAGE_DIRECTORY_NAME = 'storage';


    /**
     * @var string
     */
    protected $directory;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @param string $directory
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setDirectory($directory)
    {
        if (!is_dir($directory)) {
            throw new \InvalidArgumentException('No such directory: ' . $directory);
        }

        $this->directory = $directory;
        return $this;
    }

    /**
     * @param bool $strict
     * @return string
     * @throws \RuntimeException
     */
    public function getDirectory($strict = true)
    {
        if (!$this->directory && $strict) {
            throw new \RuntimeException('Directory is required');
        }

        return $this->directory;
    }

    /**
     * @return Filesystem
     */
    protected function getFilesystem()
    {
        if (!$this->filesystem) {
            $this->filesystem = new Filesystem();
        }

        return $this->filesystem;
    }

    /**
     * @return string
     */
    protected function getMetaDataFilePath()
    {
        return $this->getDirectory() . '/metadata.json';
    }

    protected function getStoragePath()
    {
        return $this->getDirectory() . '/' . static::STORAGE_DIRECTORY_NAME;
    }

    /**
     * @param string $key
     * @return string
     */
    protected function getFilePathByKey($key)
    {
        return $this->getStoragePath() . '/' . $key;
    }

    /**
     * @return array
     */
    protected function getMetaData()
    {
        $metadataFile = $this->getMetaDataFilePath();

        if (!$this->getFilesystem()->exists($metadataFile)) {
            $this->getFilesystem()->setContents($metadataFile, json_encode([]));
            return [];
        }

        return json_decode($this->getFilesystem()->getContents($metadataFile), true);
    }

    /**
     * @param array $metaData
     * @return $this
     */
    protected function saveMetaData(array $metaData)
    {
        $this->getFilesystem()->setContents($this->getMetaDataFilePath(), json_encode($metaData, JSON_PRETTY_PRINT));
        return $this;
    }

    /**
     * @param string $key
     * @param bool $expireCheck
     * @return bool
     */
    public function has($key, $expireCheck = true)
    {
        $metaData = $this->getMetaData();

        if (!isset($metaData[$key])) {
            return false;
        }

        $expireTime = $metaData[$key];

        if ($expireCheck && $expireTime < time()) {
            $this->remove($key);
            return false;
        }

        return true;
    }

    /**
     * @param $key
     * @param bool $success
     * @return mixed
     */
    public function get($key, &$success = null)
    {
        $success = $this->has($key);

        if ($success) {
            $filePath = $this->getFilePathByKey($key);

            if (!$this->getFilesystem()->exists($filePath)) {
                $success = false;
                return;
            }

            return unserialize($this->getFilesystem()->getContents($filePath));
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param int $expiration
     * @return $this
     */
    public function set($key, $value, $expiration)
    {
        $metaData = $this->getMetaData();
        $metaData[$key] = $expiration;

        # update metadata
        $this->saveMetaData($metaData);

        # save content
        $this->getFilesystem()->setContents($this->getFilePathByKey($key), serialize($value));

        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function remove($key)
    {
        if (!$this->has($key, false)) {
            return $this;
        }

        # remove file
        $this->getFilesystem()->remove($this->getFilePathByKey($key));

        # remove metadata key
        $metaData = $this->getMetaData();
        unset($metaData[$key]);
        $this->saveMetaData($metaData);

        return $this;
    }

    /**
     * @return $this
     */
    public function removeAll()
    {
        # remove all files
        $this->getFilesystem()->remove($this->getStoragePath());

        # update metadata
        $this->saveMetaData([]);

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param int $expiration
     * @return bool
     */
    public function replace($key, $value, $expiration = 0)
    {
        if (!$this->has($key)) {
            return false;
        }

        # update contents
        $this->getFilesystem()->setContents($this->getFilePathByKey($key), serialize($value));

        if ($expiration) {
            $metaData = $this->getMetaData();
            $metaData[$key] = $expiration;
            $this->saveMetaData($metaData);
        }

        return true;
    }

}