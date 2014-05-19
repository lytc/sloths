<div class="page-header">
    <h1>Database Connection</h1>
</div>

<textarea class="code">
    $connection = new Sloths\Db\Connection('localhost', 3306, 'username', 'password', 'my-dbname');
    $connection->query("SELECT * FROM users");
    $connection->exec("DELETE FROM users WHERE id = 1");

    $select = new Sloths\Db\Sql\Select();
    //...
    $connection->select($select);
    $connection->selectAll($select);
    $connection->selectAllWithFoundRows($select);
    $connection->selectColumn($select);

    $insert = new Sloths\Db\Sql\Insert();
    $connection->insert($insert);

    $update = new Sloths\Db\Sql\Update();
    $connection->update($update);

    $delete = new Sloths\Db\Sql\Delete();
    $connection->delete($delete);
</textarea>