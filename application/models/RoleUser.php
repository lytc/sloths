<?php

namespace Application\Model;

use Sloths\Db\Model\AbstractModel;

class RoleUser extends AbstractModel
{
    protected static $columns = [
        'id'        => self::INT,
        'role_id'   => self::INT,
        'user_id'   => self::INT,
    ];

    protected static $belongsTo = [
        'Role',
        'User'
    ];
}