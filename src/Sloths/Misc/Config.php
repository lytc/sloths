<?php

namespace Sloths\Misc;

class Config extends ArrayContainer
{
    /**
     * @param $files
     * @param bool $replaceRecursive
     * @param bool $recursive
     * @return static
     * @throws \InvalidArgumentException
     */
    public static function fromFile($files, $replaceRecursive = true, $recursive = true)
    {
        is_array($files) || $files = [$files];

        $data = [];

        foreach ($files as $file) {
            $result = call_user_func(function() use ($file) {
                $extension = pathinfo($file, PATHINFO_EXTENSION);

                switch ($extension) {
                    case 'json':
                        return json_decode(file_get_contents($file), true);

                    case 'php':
                        return require $file;

                }

                throw new \InvalidArgumentException(sprintf('Config file should be php or json file. %s given', $extension));
            });

            if (!is_array($result)) {
                throw new \InvalidArgumentException('Config file should return an array');
            }

            if ($replaceRecursive) {
                $data = array_replace_recursive($data, $result);
            } else {
                $data = array_merge($data, $result);
            }
        }

        return new static($data, $recursive);
    }
}