<?php

namespace Application\Model;

use Sloths\Authentication\Adapter\AdapterInterface;
use Sloths\Db\Model\AbstractModel;

class User extends AbstractModel
{
    protected $columns = [
        'id'                => self::INT,
        'email'             => self::VARCHAR,
        'password'          => self::VARCHAR,
        'name'              => self::VARCHAR,
        'avatar'            => self::VARCHAR,
        'phone'             => self::VARCHAR,
        'address'           => self::VARCHAR,
        'birthday'          => self::DATE,
        'remember_token'    => self::VARCHAR,
        'created_time'      => self::DATETIME,
        'modified_time'     => self::DATETIME,
        'status'            => self::TINYINT
    ];

    protected $hasMany = [
        'CreatedPosts' => [
            'model' => 'Post',
            'foreignKey' => 'creator_id'
        ],
//        'CreatedPosts' => [
//            'model' => 'Application\Model\Post',
//            'foreignKey' => 'creator_id'
//        ],
        'ModifiedPosts' => [
            'model' => 'Application\Model\Post',
            'foreignKey' => 'modifier_id'
        ],
        'Roles' => [
            'model'             => 'Role',
            'through'           => 'RoleUser',
//            'leftForeignKey'    => 'user_id',
//            'rightForeignKey'   => 'role_id',
        ],

//        'Roles' => [
//            'model'             => 'Application\Model\Role',
//            'through'           => 'Application\Model\RoleUser',
//            'leftForeignKey'    => 'user_id',
//            'rightForeignKey'   => 'role_id',
//        ],
    ];

    protected $hiddenColumns = ['password', 'remember_token'];
}