<?php
namespace LazyTest\Db\Model\Generate\AbstractModel;

/**
 * @property int id
 * @property int userId
 * @property varchar name
 * @property text content
 * @property \LazyTest\Db\Model\Generate\User User
 */
abstract class AbstractPost extends AbstractAppModel
{
    protected static $tableName = 'posts';
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
        'user_id' => array(
            'type'      => self::TYPE_INT,
            'length'    => 11,
            'unsigned'  => true,
            'default'   => null,
            'nullable'  => false,
        ),
        'name' => array(
            'type'      => self::TYPE_VARCHAR,
            'length'    => 255,
            'default'   => null,
            'nullable'  => false,
        ),
        'content' => array(
            'type'      => self::TYPE_TEXT,
            'length'    => 255,
            'default'   => null,
            'nullable'  => false,
        ),
    );

    protected static $manyToOne = array(
        'User' => array(
            'model'     => 'LazyTest\Db\Model\Generate\\User',
            'key'       => 'user_id',
        ),
    );

}