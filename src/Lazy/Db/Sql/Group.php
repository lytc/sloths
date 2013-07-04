<?php

namespace Lazy\Db\Sql;

use Lazy\Db\Connection;

/**
 * Class Group
 * @package Lazy\Db\Sql
 */
class Group
{
    /**
     * @var array
     */
    protected $groups = array();

    /**
     * @param string|array|args $group
     */
    public function __construct($group = null)
    {
        if ($group) {
            call_user_func_array(array($this, 'group'), func_get_args());
        }
    }

    /**
     * @param stirng|array|args $group
     * @return $this|array
     */
    public function group($group = null)
    {
        if (!$group) {
            return $this->groups;
        }

        $groups = func_get_args();
        foreach ($groups as $group) {
            if (is_string($group)) {
                $group = preg_split('/\s*,\s*/', trim($group));
            }
            $this->groups = array_merge($this->groups, array_combine($group, $group));
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->groups = array();
        return $this;
    }

    /**
     * @return string
     */
    public function toString()
    {
        if (!$this->groups) {
            return '';
        }

        return 'GROUP BY ' . implode(', ', $this->groups);
    }
}