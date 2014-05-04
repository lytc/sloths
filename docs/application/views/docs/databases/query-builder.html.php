<div class="page-header">
    <h1>Query Builder</h1>
</div>

<h3>Select</h3>
<h4>Simple Select</h4>
<textarea class="code">
use Lazy\Db;
use Lazy\Db\Sql\Select;

$select = new Select('users');
$select->select('name, email')
    ->where('id = 1')
    ->orderBy('name, created_time DESC')
    ->limit(10)
    ->offset(5)
    ;

// or
$select = Db::select('users', 'name, email', 'id = 1')
    ->orderBy('name, created_time DESC')
    ->limit(10)
    ->offset(5)
    ;

$select->toString();
// SELECT name, email FROM users WHERE (id = 1) ORDER BY name, created_time DESC LIMIT 10 OFFSET 5
</textarea>

<h4>Select With Join</h4>
<textarea class="code">
use Lazy\Db;

$select = Db::select();
$select->from('posts')
    ->select('title, content')
    ->join('users', 'users.id = posts.user_id')
    ->where('users.id = 1')
    ;

$select->toString();
// SELECT title, content FROM posts INNER JOIN users ON users.id = posts.user_id WHERE users.id = 1
</textarea>

<h4>Complex Where Conditions <em class="text-muted">(Having is similar to)</em></h4>
<textarea class="code">
use Lazy\Db;

$select = Db::select('users');
$select->select('name, email')
    ->where('status = ?', 'active')
    ->where(function() {
        $this->where('group_id IN(?)', [1, 2, 3])
             ->orWhere('is_admin = ?', 1)
    })
    ;

$select->toString();
/*
SELECT
    name, email
FROM users
WHERE
    (status = 'active')
    AND (
        (group_id IN(1, 2, 3))
        OR (is_admin = 1)
    )
*/
</textarea>

<h4>With SQL Options</h4>
<textarea class="code">
use Lazy\Db;

$select = Db::select();
$select->from('users')
    ->distinct()
    ->calcFoundRows()
    ->select('email');

$select->toString();
// SELECT DISTINCT, SQL_CALC_FOUND_ROWS email FROM users
</textarea>

<h3>Insert</h3>
<textarea class="code">
use Lazy\Db;
use Lazy\Db\Sql\Insert;

$insert = new Insert('users');
$insert->values(['name' => 'User Name', 'user@example.com']);

// or
$insert = Db::insert('users', ['name' => 'User Name', 'user@example.com']);

$insert->toString();
// INSERT INTO users (name, email) VALUES ('User Name', 'user@example.com')
</textarea>

<h3>Update</h3>
<textarea class="code">
use Lazy\Db;
use Lazy\Db\Sql\Update;

$update = new Insert('users');
$update->values(['name' => 'User Name', 'user@example.com'])->where('id = 1');

// or
$update = Db::update('users', ['name' => 'User Name', 'user@example.com'], 'id = 1');

$update->toString();
// UPDATE users SET name = 'User Name', email = 'user@example.com' WHERE (id = 1)
</textarea>

<h3>Delete</h3>
<textarea class="code">
use Lazy\Db;
use Lazy\Db\Sql\DELETE;

$delete = new Delete('users');
$delete->where('id = 1');

// or
$delete = Db::delete('users', 'id = 1');

$delete->toString();
// DELETE FROM users WHERE (id = 1)
</textarea>