<?php
namespace LazyTest\Db\Model\Generate\AbstractModel;

/**
 * @property int userId
 * @property int permissionId
 * @property \LazyTest\Db\Model\Generate\User User
 * @property \LazyTest\Db\Model\Generate\Permission Permission
 */
abstract class AbstractUserPermission extends AbstractAppModel
{
    protected static $tableName = 'user_permissions';
    protected static $columns = array(
        'user_id' => array(
            'type'      => self::TYPE_INT,
            'length'    => 11,
            'unsigned'  => true,
            'default'   => null,
            'nullable'  => false,
        ),
        'permission_id' => array(
            'type'      => self::TYPE_INT,
            'length'    => 11,
            'unsigned'  => true,
            'default'   => null,
            'nullable'  => false,
        ),
    );

    protected static $manyToOne = array(
        'User' => array(
            'model'     => 'LazyTest\Db\Model\Generate\\User',
            'key'       => 'user_id',
        ),
        'Permission' => array(
            'model'     => 'LazyTest\Db\Model\Generate\\Permission',
            'key'       => 'permission_id',
        ),
    );

}