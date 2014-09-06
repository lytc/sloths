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
     * @param string $path
     * @param string $user
     * @param bool $recursive
     * @return bool
     */
    public function chown($path, $user, $recursive = true)
    {
        if (is_link($path)) {
            return lchown($path, $user);
        }

        if (!$recursive || $this->isFile($path)) {
            return chown($path, $user);
        }

        foreach (new \FilesystemIterator($path) as $f) {
            $this->chown($f, $user, $recursive);
        }

        return chown($path, $user);
    }

    /**
     * @param string $path
     * @param string $group
     * @param bool $recursive
     * @return bool
     */
    public function chgrp($path, $group, $recursive = true)
    {
        if (is_link($path)) {
            return lchgrp($path, $group);
        }

        if (!$recursive || $this->isFile($path)) {
            return chgrp($path, $group);
        }

        foreach (new \FilesystemIterator($path) as $f) {
            $this->chgrp($f, $group, $recursive);
        }

        return chgrp($path, $group);
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
    public function copy($source, $dest)
    {
        if (is_dir($source)) {
            return $this->copyDir($source, $dest);
        }

        return copy($source, $dest);
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
    public function files($dir)
    {
        return $this->glob($dir . '/*.*');
    }

    /**
     * @param string $dir
     * @return array
     */
    public function directories($dir)
    {
        return $this->glob($dir . '/*', GLOB_ONLYDIR);
    }

    /**
     * @param string $path
     * @return string
     */
    public function dirname($path)
    {
        return pathinfo($path, PATHINFO_DIRNAME);
    }

    /**
     * @param string $path
     * @return string
     */
    public function basename($path)
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }

    /**
     * @param string $path
     * @return string
     */
    public function name($path)
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * @param string $path
     * @return string
     */
    public function extension($path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }
}