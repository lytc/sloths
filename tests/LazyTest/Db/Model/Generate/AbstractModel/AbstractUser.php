<?php
namespace LazyTest\Db\Model\Generate\AbstractModel;

/**
 * @property int id
 * @property varchar name
 * @property \Lazy\Db\Collection UserPermissions
 * @property \Lazy\Db\Collection Products
 * @property \Lazy\Db\Collection Permissions
 */
abstract class AbstractUser extends AbstractAppModel
{
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
            'key'       => 'user_id',
        ),
    );

    protected static $manyToMany = array(
        'Products' => array(
            'model'     => 'LazyTest\Db\Model\Generate\\Product',
            'through'   => 'LazyTest\Db\Model\Generate\\Order',
            'leftKey'   => 'user_id',
            'rightKey'  => 'product_id',
        ),
        'Permissions' => array(
            'model'     => 'LazyTest\Db\Model\Generate\\Permission',
            'through'   => 'LazyTest\Db\Model\Generate\\UserPermission',
            'leftKey'   => 'user_id',
            'rightKey'  => 'permission_id',
        ),
    );

}