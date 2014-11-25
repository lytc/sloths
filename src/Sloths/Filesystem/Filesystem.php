<?php

namespace Sloths\Filesystem;

class Filesystem
{
    /**
     * @param $path
     * @return bool
     */
    public function exists($path)
    {
        return file_exists($path);
    }

    /**
     * @param string $path
     * @return bool
     */
    public function isFile($path)
    {
        return is_file($path);
    }

    /**
     * @param string $path
     * @return bool
     */
    public function isLink($path)
    {
        return is_link($path);
    }

    /**
     * @param string $path
     * @return bool
     */
    public function isDir($path)
    {
        return is_dir($path);
    }

    /**
     * @param string $path
     * @return bool
     */
    public function isReadable($path)
    {
        return is_readable($path);
    }

    /**
     * @param $path
     * @return bool
     */
    public function isWritable($path)
    {
        return is_writable($path);
    }

    /**
     * @param string $path
     * @param bool $clearRealPathCache
     */
    public function clearStatCache($path = null, $clearRealPathCache = false)
    {
        clearstatcache($clearRealPathCache, $path);
    }

    /**
     * @param string $path
     * @return int
     */
    public function getMode($path)
    {
        return fileperms($path) & 0777;
    }

    /**
     * @param string $dir
     * @param int $mode
     * @param bool $recursive
     * @return bool
     */
    public function mkdir($dir, $mode = 0777, $recursive = true)
    {
        if (is_dir($dir)) {
            return true;
        }

        return mkdir($dir, $mode, $recursive);
    }

    /**
     * @param string $path
     * @return bool
     */
    public function remove($path)
    {
        if ($this->isFile($path) || $this->isLink($path)) {
            return unlink($path);
        }

        foreach (new \FilesystemIterator($path) as $f) {
            $this->remove($f);
        }

        return rmdir($path);
    }

    /**
     * @param string $path
     * @param int $mode
     * @param bool $recursive
     * @return bool
     */
    public function chmod($path, $mode, $recursive = true)
    {
        if (!$recursive || $this->isFile($path) || $this->isLink($path)) {
            return chmod($path, $mode);
        }
        foreach (new \FilesystemIterator($path) as $f) {
            $this->chmod($f, $mode, $recursive);
        }

        return chmod($path, $mode);
    }

    /**
     * @param string $old
     * @param string $new
     * @return bool
     */
    public function rename($old, $new)
    {
        return rename($old, $new);
    }

    /**
     * @param string $source
     * @param string $dest
     * @return bool
     */
    public function copyDir($source, $dest)
    {
        $this->mkdir($dest);

        foreach (new \FilesystemIterator($source) as $f) {
            /* @var $f \SplFileInfo */
            $this->copy($f->getPathname(), $dest . '/' . $f->getBasename());
        }

        return true;
    }

    /**
     * @param string $source
     * @param string $dest
     * @return bool
     */
    public function copy($source, $dest)
    {
        if (is_dir($source)) {
            return $this->copyDir($source, $dest);
        }

        return copy($source, $dest);
    }

    /**
     * @param string $path
     * @return string
     */
    public function getContents($path)
    {
        return file_get_contents($path);
    }

    /**
     * @param string $path
     * @param mixed $contents
     * @param int $flags
     * @return int
     */
    public function putContents($path, $contents, $flags = 0)
    {
        $this->mkdir($this->getDirName($path));
        return file_put_contents($path, $contents, $flags);
    }

    /**
     * @param string $path
     * @param mixed $contents
     * @return int
     */
    public function setContents($path, $contents)
    {
        return $this->putContents($path, $contents);
    }

    /**
     * @param string $path
     * @param mixed $contents
     * @return int
     */
    public function appendContents($path, $contents)
    {
        return $this->putContents($path, $contents, FILE_APPEND);
    }

    /**
     * @param string $path
     * @param mixed $contents
     * @return int
     */
    public function prependContents($path, $contents)
    {
        return $this->putContents($path, $contents . file_get_contents($path));
    }

    /**
     * @param string $pattern
     * @param int $flags
     * @return array
     */
    public function glob($pattern, $flags = 0)
    {
        return glob($pattern, $flags);
    }

    /**
     * @param string $dir
     * @return array
     */
    public function listFiles($dir)
    {
        return array_filter($this->glob($dir . '/*'), 'is_file');
    }

    /**
     * @param string $dir
     * @return array
     */
    public function listDirectories($dir)
    {
        return $this->glob($dir . '/*', GLOB_ONLYDIR);
    }

    /**
     * @param string $path
     * @return string
     */
    public function getDirName($path)
    {
        return pathinfo($path, PATHINFO_DIRNAME);
    }

    /**
     * @param string $path
     * @return string
     */
    public function getBaseName($path)
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }

    /**
     * @param string $path
     * @return string
     */
    public function getName($path)
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * @param string $path
     * @return string
     */
    public function getExtension($path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }
}