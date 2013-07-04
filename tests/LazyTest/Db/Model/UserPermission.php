<?php

namespace LazyTest\Db\Model;

use Lazy\Db\AbstractModel;

class UserPermission extends AbstractModel
{
    protected static $columns = array(
        'user_id'            => 'int',
        'permission_id'      => 'int',
    );
}