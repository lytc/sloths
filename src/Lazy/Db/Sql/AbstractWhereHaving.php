<?php

namespace Lazy\Db\Sql;
use Lazy\Db\Db;

abstract class AbstractWhereHaving
{
    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var array
     */
    protected $conditions = [];

    /**
     * @param string $type
     * @param mixed $condition
     * @param mixed $params
     * @return $this
     */
    protected function addCondition($type, $condition, $params = null)
    {
        $condition = [
            'type'      => $type,
            'pattern'   => $condition,
        ];

        $numArgs = func_num_args();
        if ($numArgs == 3) {
            is_array($params) || $params = [$params];
            $condition['params'] = $params;
        } else if ($numArgs > 3) {
            $params = array_slice(func_get_args(), 2);
            $condition['params'] = $params;
        }

        $this->conditions[] = $condition;

        return $this;
    }

    /**
     * @return $this
     */
    protected function addAndCondition()
    {
        $args = func_get_args();
        array_unshift($args, 'AND');
        return call_user_func_array([$this, 'addCondition'], $args);
    }

    /**
     * @return $this
     */
    protected function addOrCondition()
    {
        $args = func_get_args();
        array_unshift($args, 'OR');
        return call_user_func_array([$this, 'addCondition'], $args);
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->conditions = [];
        return $this;
    }

    /**
     * @param string $pattern
     * @param array $params
     * @return string
     */
    protected function bind($pattern, array $params)
    {
        $params = array_values($params);
        $lastValue = end($params);

        # where('foo', 'bar') => foo = 'bar'
        # where('`foo`', 'bar') => `foo` = 'bar'
        # where('foo', null) => `foo` IS NULL
        if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $pattern) || preg_match('/^`[a-zA-Z_][a-zA-Z0-9_]*`$/', $pattern)) {
            if ($lastValue === null) {
                return $pattern . ' IS NULL';
            } else {
                return $pattern . ' = ' . Db::quote($lastValue);
            }
        }
        # where('foo = ?', null) => foo IS NULL
        # where('foo != ?', null) => `foo` IS NOT NULL
        elseif ($lastValue === null && 1 == count($params)
            && (
                preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*) (\!)?= \?$/', $pattern, $matches)
                || preg_match('/^(`[a-zA-Z_][a-zA-Z0-9_]*`) (\!)?= \?$/', $pattern, $matches))) {
            return $matches[1] . ($matches[2]? ' IS NOT NULL' : 'IS NULL');
        }
        # where('foo IN(?)', [1, 2]) => foo IN(1, 2)
        # where('`foo` IN(?)', [1, 2]) => `foo` IN(1, 2)
        elseif (preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*) IN\(\?\)$/', $pattern, $matches) || preg_match('/^(`[a-zA-Z_][a-zA-Z0-9_]*`) IN\(\?\)$/', $pattern, $matches)) {
            $params = Db::quote($params);
            return $matches[1] . ' IN(' . implode(', ', $params) . ')';
        }
        # where('foo LIKE %?%', 'foo') => foo LIKE '%foo%'
        elseif (preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*) LIKE (%)?\?(%)?$/', $pattern, $matches) || preg_match('/^(`[a-zA-Z_][a-zA-Z0-9_]*`) LIKE (%)?\?(%)?$/', $pattern, $matches)) {
            return $matches[1] . ' LIKE \'' . ($matches[2]?: '') . Db::escape($lastValue) . ($matches[3]?: '') . '\'';
        }

            $index = 0;

        return preg_replace_callback('/\?/', function($matches) use ($params, $lastValue, &$index) {
            $value = array_key_exists($index, $params)? $params[$index] : $lastValue;
            $index++;
            return Db::quote($value);
        }, $pattern);
    }

    /**
     * @param array $condition
     * @return array
     */
    protected function compilePart(array $condition)
    {
        if (is_callable($condition['pattern'])) {
            $patternCallback = $condition['pattern'];
            $conditionClass = static::class;
            $conditionInstance = new $conditionClass;

            if ($patternCallback instanceof \Closure) {
                $patternCallback = $patternCallback->bindTo($conditionInstance);
            }

            call_user_func($patternCallback, $conditionInstance);

            return $conditionInstance->toString(false);
        }

        if (is_array($condition['pattern'])) {
            $compiledPattern = [];
            foreach ($condition['pattern'] as $pattern => $params) {
                $compiled = $this->compilePart([
                    'pattern' => $pattern,
                    'params' => is_array($params)? $params : [$params]
                ]);
                $compiledPattern[] = $compiled;
            }
            $compiledPattern = implode(' AND ', $compiledPattern);
        } else {
            if (!isset($condition['params'])) {
                $compiledPattern = $condition['pattern'];
            } else {
                $compiledPattern = $this->bind($condition['pattern'], $condition['params']);
            }
        }

        return $compiledPattern;
    }

    /**
     * @param bool $withPrefix
     * @return string
     */
    public function toString($withPrefix = true)
    {
        if (!$this->conditions) {
            return '';
        }

        $parts = [];
        foreach ($this->conditions as $index => $condition) {
            $compiled = $this->compilePart($condition);
            if (0 == $index) {
                $parts[] = '(' . $compiled . ')';
            } else {
                $parts[] = $condition['type'] . ' (' . $compiled . ')';
            }
        }

        if ($withPrefix) {
            array_unshift($parts, $this->prefix);
        }

        return implode(' ', $parts);
    }
}