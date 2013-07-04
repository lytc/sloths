<?php
namespace LazyTest\Db\Model\Generate\AbstractModel;

/**
 * @property int id
 * @property int userId
 * @property int productId
 * @property tinyint status
 * @property \LazyTest\Db\Model\Generate\Product Product
 * @property \LazyTest\Db\Model\Generate\User User
 */
abstract class AbstractOrder extends AbstractAppModel
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
        'user_id' => array(
            'type'      => self::TYPE_INT,
            'length'    => 11,
            'unsigned'  => true,
            'default'   => null,
            'nullable'  => false,
        ),
        'product_id' => array(
            'type'      => self::TYPE_INT,
            'length'    => 11,
            'unsigned'  => true,
            'default'   => null,
            'nullable'  => false,
        ),
        'status' => array(
            'type'      => self::TYPE_TINYINT,
            'length'    => 1,
            'default'   => '0',
            'nullable'  => false,
        ),
    );

    protected static $manyToOne = array(
        'Product' => array(
            'model'     => 'LazyTest\Db\Model\Generate\\Product',
            'key'       => 'product_id',
        ),
        'User' => array(
            'model'     => 'LazyTest\Db\Model\Generate\\User',
            'key'       => 'user_id',
        ),
    );

}