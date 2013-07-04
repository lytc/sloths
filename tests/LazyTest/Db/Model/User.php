<?php

namespace LazyTest\Db\Model;

use Lazy\Db\AbstractModel;

class User extends AbstractModel
{
    protected static $columns = array(
        'id'            => 'int',
        'name'          => 'varchar',
    );

    protected static $defaultSelectColumns = array('id', 'name');
    protected static $lazyLoadColumns = array();

    protected static $oneToMany = array('Orders', 'Posts');
    protected static $manyToMany = array('Permissions');
}