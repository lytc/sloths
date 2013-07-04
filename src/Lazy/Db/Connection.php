<?php

namespace Lazy\Db;

use PDO;
use Lazy\Db\Exception;

/**
 * Class Connection
 * @package Lazy\Db
 */
class Connection extends PDO
{
    /**
     *
     */
    const ENV_PRODUCTION    = 'production';

    /**
     *
     */
    const ENV_DEVELOPMENT   = 'development';

    /**
     *
     */
    const ENV_TEST          = 'test';

    /**
     *
     */
    const IDENTIFIER_BACK_TICK = '`';

    /**
     *
     */
    const IDENTIFIER_DOUBLE_QUOTES = '"';

    /**
     * @var string
     */
    protected static $env = self::ENV_PRODUCTION;

    /**
     * @var array
     */
    protected static $defaultConfig = array();

    /**
     * @var array
     */
    protected static $configs = array();

    /**
     * @var Connection
     */
    protected static $defaultInstance;

    /**
     * @var array
     */
    protected static $instances = array();

    /**
     * @var string
     */
    protected static $quoteIdentifierSymbol = self::IDENTIFIER_BACK_TICK;

    /**
     * @param $env
     */
    public static function setEnv($env)
    {
        self::$env = $env;
    }

    /**
     * @return string
     */
    public static function getEnv()
    {
        return self::$env;
    }

    /**
     * @param array $config
     */
    public static function setDefaultConfig(array $config)
    {
        self::$defaultConfig = $config;
    }

    /**
     * @return array
     */
    public static function getDefaultConfig()
    {
        return self::$defaultConfig;
    }

    /**
     * @param string $id
     * @param array $config
     */
    public static function setConfig($id, array $config)
    {
        self::$configs[$id] = $config;
    }

    /**
     * @param string $id
     * @return array|null
     */
    public static function getConfig($id)
    {
        if (isset(self::$configs[$id])) {
            return self::$configs[$id];
        }
    }

    /**
     * @param string $dsn
     * @param string $username optional
     * @param string $password optional
     * @param array $options optional
     */
    public function __construct($dsn, $username = null, $password = null, array $options = array())
    {
        $options[\PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES 'utf8'";
        parent::__construct($dsn, $username, $password, $options);
        $this->setAttribute(self::ATTR_ERRMODE, self::ERRMODE_EXCEPTION);
        $this->setAttribute(self::ATTR_STATEMENT_CLASS, array(__NAMESPACE__ . '\\Statement'));

    }

    /**
     * @param string $id optional
     * @return static
     * @throws Exception
     */
    protected static function _getInstance($id = null)
    {
        if (!$id) {
            if (self::$defaultInstance) {
                return self::$defaultInstance;
            }
        } elseif (isset(self::$instances[$id])) {
            return self::$instances[$id];
        }

        if (!$id) {
            $config = self::$defaultConfig;
        } elseif (isset(self::$configs[$id])) {
            $config = self::$configs[$id];
        }

        if (empty($config)) {
            throw new Exception('Connection configuration was not set');
        }

        $env = self::getEnv();

        if (!isset($config[$env])) {
            throw new Exception(sprintf('Undefined configuration for environment %s', $env));
        }

        $config = $config[$env];

        $username = null;
        $password = null;
        $options = array();

        if (is_string($config)) {
            $dsn = $config;
        } else {
            if (!isset($config['dsn'])) {
                throw new Exception('Undefined configuration property dsn');
            }

            $dsn = $config['dsn'];

            if (isset($config['username'])) {
                $username = $config['username'];
            }

            if (isset($config['password'])) {
                $password = $config['password'];
            }

            if (isset($config['options'])) {
                $options = $config['options'];
            }
        }

        $instance = new static($dsn, $username, $password, $options);

        if (!$id) {
            self::$defaultInstance = $instance;
        } else {
            self::$instances[$id] = $instance;
        }

        return $instance;
    }

    /**
     * @return Connection
     */
    public static function getDefaultInstance()
    {
        return self::_getInstance();
    }

    /**
     * @param $id
     * @return Connection
     */
    public static function getInstance($id)
    {
        return self::_getInstance($id);
    }

    /**
     * @return string
     */
    public static function getQuoteIdentifierSymbol()
    {
        return self::$quoteIdentifierSymbol;
    }

    /**
     * @param string $symbol
     */
    public static function setQuoteIdentifierSymbol($symbol)
    {
        self::$quoteIdentifierSymbol = $symbol;
    }

    /**
     * @param string|array $identifier
     * @return string
     */
    public static function quoteIdentifier($identifier)
    {
        if (is_array($identifier)) {
            foreach ($identifier as &$item) {
                $item = self::quoteIdentifier($item);
            }

            return $identifier;
        }

        $symbol = self::getQuoteIdentifierSymbol();
        return $symbol  .$identifier  .$symbol;
    }

    /**
     * @param mixed $value
     * @param string $type optional
     * @return array|int|string
     */
    public function quote($value, $type = null)
    {
        if ($value instanceof Expr || $value instanceof Select) {
            return $value->toString();
        }

        if (!$type) {
            $type = gettype($value);
        }

        switch ($type) {
            case 'boolean': return (int) !!$value;
            case 'integer': return (int) $value;
            case 'NULL': return 'NULL';
            case 'array':
                $value = (array) $value;
                foreach ($value as &$v) {
                    $v = $this->quote($v);
                }
                return $value;

            default: return parent::quote($value);
        }
    }

    /**
     * @param mixed $str
     * @return string
     */
    public function escape($str)
    {
        if ($str instanceof Expr || $str instanceof Select) {
            return $str->toString();
        }

        return substr(parent::quote($str), 1, -1);
    }
}