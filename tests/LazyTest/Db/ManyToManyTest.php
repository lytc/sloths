<?php

namespace LazyTest\Db;

use LazyTest\Db\Model\Permission;
use LazyTest\Db\Model\User;

/**
 * @covers Lazy\Db\AbstractModel
 */
class ManyToManyTest extends TestCase
{
    public function test()
    {
        $user = User::first(1);
        $permissions = $user->Permissions;

        $expectedSql = "SELECT permissions.id, permissions.name FROM permissions " .
                       "INNER JOIN user_permissions ON user_permissions.permission_id = permissions.id " .
                       "WHERE (user_permissions.user_id = '1')";

        $this->assertSame($expectedSql, $permissions->getSqlSelect()->toString());

        $permission = Permission::first(1);
        $users = $permission->Users;
        $expectedSql = "SELECT users.id, users.name FROM users " .
            "INNER JOIN user_permissions ON user_permissions.user_id = users.id " .
            "WHERE (user_permissions.permission_id = '1')";

        $this->assertSame($expectedSql, $users->getSqlSelect()->toString());

    }
}