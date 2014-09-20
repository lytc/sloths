<?php

namespace Application\Model;

use Sloths\Db\Model\AbstractModel;

class Post extends AbstractModel
{
    protected $columns = [
        'id'            => self::INT,
        'creator_id'    => self::INT,
        'modifier_id'   => self::INT,
        'title'         => self::VARCHAR,
        'thumbnail'     => self::VARCHAR,
        'summary'       => self::TEXT,
        'content'       => self::TEXT,
        'created_time'  => self::DATETIME,
        'modified_time' => self::DATETIME
    ];

    protected $belongsTo = [
        'Creator' => 'User',
//        'Creator' => [
//            'model' => 'Application\Model\User',
//            'foreignKey' => 'creator_id',
//        ],
        'Modifier' => [
            'model' => 'Application\Model\User',
            'foreignKey' => 'modifier_id'
        ]
    ];
}