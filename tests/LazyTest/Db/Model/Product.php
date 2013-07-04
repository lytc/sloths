<?php

namespace LazyTest\Db\Model;

use Lazy\Db\AbstractModel;

class Product extends AbstractModel
{
    protected static $columns = array(
        'id'            => 'int',
        'name'          => 'varchar',
    );

    protected static $oneToMany = array('Orders');
}