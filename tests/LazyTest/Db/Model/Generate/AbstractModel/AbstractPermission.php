<?php
namespace LazyTest\Db\Model\Generate\AbstractModel;

/**
 * @property int id
 * @property varchar name
 * @property \Lazy\Db\Collection UserPermissions
 * @property \Lazy\Db\Collection Users
 */
abstract class AbstractPermission extends AbstractAppModel
{
    protected static $tableName = 'permissions';
    protected static $primaryKey = 'id';
    protected static $columns = array(
        'id' => array(
            'type'      => self::TYPE_INT,
            'length'    => 11,
            'unsigned'  => true,
            'default'   => null,
            'auto'      => true,
            'nullable'  => false,
        ),
        'name' => array(
            'type'      => self::TYPE_VARCHAR,
            'length'    => 255,
            'default'   => null,
            'nullable'  => false,
        ),
    );

    protected static $oneToMany = array(
        'UserPermissions' => array(
            'model'     => 'LazyTest\Db\Model\Generate\\UserPermission',
            'key'       => 'permission_id',
        ),
    );

    protected static $manyToMany = array(
        'Users' => array(
            'model'     => 'LazyTest\Db\Model\Generate\\User',
            'through'   => 'LazyTest\Db\Model\Generate\\UserPermission',
            'leftKey'   => 'permission_id',
            'rightKey'  => 'user_id',
        ),
    );

}