<?php

namespace Lazy\Util;

use Lazy\Util\InstanceManager\Exception\Exception;

class InstanceManager
{
    protected static $instanceOptions = [];
    protected static $instances = [];

    public static function register($id, $className = null, $args = [], $callback = null)
    {
        if (is_object($id)) {
            self::$instances[get_class($id)] = $id;
        } elseif (is_object($className)) {
            self::$instances[$id] = $className;
        } else {
            if (!is_string($className)) {
                $callback = $args;
                $args = $className;
                $className = $id;
            }

            if ($callback && !$callback instanceof \Closure) {
                throw new Exception(sprintf('Callback must be a \Closure. %s given', gettype($callback)));
            }

            self::$instanceOptions[$id] = [
                'className' => $className,
                'args'      => $args,
                'callback'  => $callback
            ];
        }
    }

    public static function get($id, $singleton = true)
    {
        if ($singleton) {
            if (!isset(self::$instances[$id])) {
                self::$instances[$id] = self::create($id);
            }

            if (isset(self::$instances[$id])) {
                return self::$instances[$id];
            }

            throw new Exception(sprintf('Instance for %s does not registered'));
        } else {
            return self::create($id);
        }
    }

    protected static function create($id)
    {
        if (isset(self::$instanceOptions[$id])) {
            $options = self::$instanceOptions[$id];

            $className = $options['className'];
            $args = $options['args'];
            if ($args instanceof \Closure) {
                $args = $args();
            }
            $args = (array) $args;
            $args = array_values($args);

            # prevent reflection in some common case
            switch (count($args)) {
                case 0:
                    $instance = new $className();
                    break;

                case 1:
                    $instance = new $className($args[0]);
                    break;

                case 2:
                    $instance = new $className($args[0], $args[1]);
                    break;

                case 3:
                    $instance = new $className($args[0], $args[1], $args[2]);
                    break;

                case 4:
                    $instance = new $className($args[0], $args[1], $args[2], $args[3]);
                    break;

                default:
                    $ref = new \ReflectionClass($options['className']);
                    $instance = $ref->newInstanceArgs($options['args']);
            }

            if ($options['callback']) {
                $options['callback']($instance);
            }
            return $instance;
        }

        throw new Exception(sprintf('Instance for %s does not registered', $id));
    }

    public static function __callStatic($method, $args)
    {
        isset($args[0]) || $args[0] = true;
        return self::get($method, $args[0]);
    }
}