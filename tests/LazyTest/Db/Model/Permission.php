<?php

namespace LazyTest\Db\Model;

use Lazy\Db\AbstractModel;

class Permission extends AbstractModel
{
    protected static $columns = array(
        'id'            => 'int',
        'name'          => 'varchar',
    );

    protected static $manyToMany = array(
        'Users' => 'UserPermission'
    );
}