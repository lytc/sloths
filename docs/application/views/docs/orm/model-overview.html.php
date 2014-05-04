<div class="page-header">
    <h1>Model Overview</h1>
</div>

<p>Assume we have models defined in <a href="/docs/databases/define-model">Defining Models</a></p>

<h4>Retrieving A Record By Primary Key</h4>
<textarea class="code">
$user = User::first(1);
echo $user->name;
</textarea>

<h4>Retrieving A Record By Conditions</h4>
<textarea class="code">
$user = User::first('email = ?', 'user@example.com');
</textarea>
<br>

<div class="alert alert-info">
    By the convention, the Model property name is processed by camelCase.
    For example, if the users table has column <code>created_time</code>. To get the <code>created_time</code> value through model,
    you should use: <code>$user->createdTime</code>
</div>

<h4>Retrieving Multiple Records <em class="text-muted">AKA Collection</em></h4>
<textarea class="code">
$users = User::all();
</textarea>

<h4>Retrieving Multiple Records By Primary Key List</h4>
<textarea class="code">
$users = User::all([1, 2, 3]);
</textarea>

<h4>Retrieving Multiple Records By Conditions</h4>
<textarea class="code">
$users = User::all(['status = ?' => 1]);
// or
$users = User::all()->where('status = ?', 1);
</textarea>

<br>
<div class="alert alert-info">
    Almost all sql Select method is visible in Collection:
    <code>select</code>,
    <code>distinct</code>,
    <code>calcFoundRows</code>,
    <code>where</code>,
    <code>orWhere</code>,
    <code>having</code>,
    <code>orHaving</code>,
    <code>orderBy</code>,
    <code>groupBy</code>,
    <code>limit</code>,
    <code>offset</code>,
    <code>join</code>,
    <code>leftJoin</code>,
    <code>rightJoin</code>,
</div>

<div class="alert alert-info">
    Sql querying in Collection is <b><em>Lazy</em></b>,
    it mean there are no query is executed until you interacting with the collection data.
    So you could adding more query filter to the collection later.
<textarea class="code">
$users = User::all(); // no sql query executing here
$users->where('status = ?', 1); // no sql query executing here too
$users->toArray(); // That is! The query executed is: SELECT users.* FROM users WHERE (status = 1)
</textarea>
</div>

<div class="alert alert-info">
    The Model Collection class is implemented of
    <code>Countable</code>,
    <code>IteratorAggregate</code>,
    <code>JsonSerializable</code>, <code>ArrayAccess</code>.
    So you could counting, iterate, encode as json or interacting it as array style.
<textarea class="code">
$users = User::all();

// Counting
count($users);

// json encoding
json_encode($users);

// iterate
foreach ($users as $user) {
    echo $user->name;
}

// interacting as array
$user = $users[0];
</textarea>
</div>