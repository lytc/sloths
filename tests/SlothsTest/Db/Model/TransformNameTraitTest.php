<?php

namespace SlothsTest\Db\Model;

use Sloths\Db\Model\TransformNameTrait;
use SlothsTest\TestCase;

/**
 * @cover Sloths\Db\Model\TransformNameTrait
 */
class TransformNameTraitTest extends TestCase
{
    public function test()
    {
        $transform = new TransformName();
        $this->assertSame('UserRole', $transform->transformTableNameToClassName('user_roles'));
        $this->assertSame('user_roles', $transform->transformClassNameToTableName('UserRole'));
        $this->assertSame('createdTime', $transform->transformColumnNameToPropertyName('created_time'));
        $this->assertSame('created_time', $transform->transformPropertyNameToColumnName('createdTime'));
        $this->assertSame('user_id', $transform->transformToForeignKeyColumnName('user'));
    }
}

class TransformName
{
    use TransformNameTrait;
}