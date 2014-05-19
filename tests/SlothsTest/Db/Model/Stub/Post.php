<?php

namespace SlothsTest\Db\Model\Stub;

use Sloths\Db\Model\Model;

class Post extends Model
{
    protected static $columns = [
        'id'                => self::INT,
        'created_user_id'   => self::INT,
        'modified_user_id'  => self::INT,
        'name'              => self::VARCHAR,
        'content'           => self::TEXT,
    ];

    protected static $belongsTo = [
        'CreatedUser' => 'User'
    ];
}