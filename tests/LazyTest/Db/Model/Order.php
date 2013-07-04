<?php

namespace LazyTest\Db\Model;

use Lazy\Db\AbstractModel;

class Order extends AbstractModel
{
    protected static $columns = array(
        'id'            => 'int',
        'user_id'       => 'int',
        'product_id'    => 'int',
        'status'        => 'tinyint',
    );

    protected static $manyToOne = array('User', 'Product');
}