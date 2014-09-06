<?php

namespace SlothsTest\Db\Model\Stub;

use Sloths\Db\Model\AbstractModel;

class Post extends AbstractModel
{
    protected static $columns = [
        'id' => self::INT,
        'user_id' => self::INT,
        'title' => self::VARCHAR,
        'content' => self::TEXT,
    ];

    protected static $belongsTo = [
        'User' => [
            'touchOnSave' => true
        ]
    ];
}