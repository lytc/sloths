<?php

namespace SlothsTest\Db\Model\Stub;

use Sloths\Db\Model\Model;

class Professor extends Model
{
    protected static $primaryKey = 'user_id';

    protected static $columns = [
        'user_id'   => self::INT,
        'title'     => self::VARCHAR,
        'resume'    => self::TEXT
    ];
}