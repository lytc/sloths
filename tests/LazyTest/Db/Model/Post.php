<?php

namespace LazyTest\Db\Model;

use Lazy\Db\AbstractModel;

class Post extends AbstractModel
{
    protected static $tableName = 'posts';

    protected static $columns = array(
        'id'            => 'int',
        'user_id'       => 'int',
        'name'          => 'varchar',
        'content'       => 'text',
    );

    protected static $manyToOne = array('User');
}