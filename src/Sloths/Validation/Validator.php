<?php

namespace Sloths\Validation;

use Sloths\Translation\Translator;
use Sloths\Validation\Rule\Not;

class Validator
{
    /**
     * @var array
     */
    protected static $namespaces = [
        'Sloths\Validation\Rule' => 'Sloths\Validation\Rule'
    ];

    /**
     * @var Translator
     */
    protected static $defaultTranslator;

    /**
     * @param string $namespace
     */
    public static function addNamespace($namespace)
    {
        static::$namespaces[$namespace] = $namespace;
    }

    /**
     * @param Translator $translator
     */
    public static function setDefaultTranslator(Translator $translator)
    {
        static::$defaultTranslator = $translator;
    }

    /**
     * @return Translator
     */
    public static function getDefaultTranslator()
    {
        if (!static::$defaultTranslator) {
            static::$defaultTranslator = new Translator();
            static::$defaultTranslator->setDirectory(__DIR__);
        }

        return static::$defaultTranslator;
    }

    /**
     * @param string $name
     * @param array $args
     * @return Rule\AbstractRule
     * @throws \InvalidArgumentException
     */
    public static function createRule($name, array $args = [])
    {
        $ruleClassName = null;

        foreach (static::$namespaces as $namespace) {
            $ruleClassName = $namespace . '\\' . ucfirst($name);

            if (class_exists($ruleClassName)) {
                break;
            } else {
                $ruleClassName = null;
            }
        }

        if (!$ruleClassName) {
            throw new \InvalidArgumentException(
                'Call to undefined rule ' . $name
            );
        }

        $reflector = new \ReflectionClass($ruleClassName);
        $rule = $reflector->newInstanceArgs($args);

        if (!$rule instanceof ValidatableInterface) {
            throw new \InvalidArgumentException(
                sprintf('Expects rule must be instance of %s, instanceof %s given',
                    __NAMESPACE__ . '\\ValidatableInterface', get_class($rule)
                )
            );
        }

        return $rule;
    }

    /**
     * @param $name
     * @param $args
     * @return ValidatableInterface
     */
    public static function __callStatic($name, $args)
    {
        if ('not' == substr($name, 0, 3)) {
            return new Not(static::createRule(substr($name, 3), $args));
        }

        return static::createRule($name, $args);
    }
}