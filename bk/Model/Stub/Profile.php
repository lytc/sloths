<?php

namespace SlothsTest\Db\Model\Stub;

use Sloths\Db\Model\AbstractModel;

class Profile extends AbstractModel
{
    protected static $columns = [
        'id' => self::INT,
        'user_id' => self::INT,
        'title' => self::VARCHAR,
        'resume' => self::TEXT
    ];
}