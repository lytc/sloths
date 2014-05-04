<div class="page-header">
    <h1>Model Collection</h1>
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

<p>Collection have 2 fallback method to model: <code>save</code> and <code>delete</code> and the model property setter.</p>
<textarea class="code">
$users = User::all();

// mass update models
$users->status = 'active';
$users->save();

// mass delete models
$users->delete();
</textarea>

<h4>Lazy Loading</h4>
<p>
    Lazy Model support <b>Lazy Loading</b>. By default, the column types below considering as <b>Lazy Loading</b>:
    <ul>
    <li><code>Model::TEXT (text)</code>
    <li><code>Model::MEDIUMTEXT (mediumtext)</code>
    <li><code>Model::LONGTEXT (longtext)</code>
    <li><code>Model::BLOB (blob)</code>
    <li><code>Model::MEDIUMBLOB (mediumblob)</code>
    <li><code>Model::LONGBLOB (longblob)</code>
    </ul>
    Feel free to custom you own lazy loading column by defining property
    <code>$defaultLazyLoadColumnTypes</code> or <code>$defaultSelectColumns</code> in your model definition.
</p>

<textarea class="code">
$posts = Posts::all([1, 2, 3]);

foreach ($posts as $post) {
    echo $post->title;
}

/*
The query executed is:
SELECT
    posts.id, posts.creator_id, posts.title, posts.created_time, posts.modified_time
FROM posts
WHERE (id IN(1, 2, 3))
*/
</textarea>

<p>
    As you see. The column <code>content</code> of posts table not selected yet. But when the first time you get the content property,
    it will select the <code>content</code> column related to the collection.
</p>
<textarea class="code">
foreach ($posts as $post) {
    echo $post->content;
}

/*
The second query executed is:
SELECT posts.id, posts.content FROM posts WHERE (id IN(1, 2, 3))
*/
</textarea>

<h4 id="eager-loading">Eager Loading</h4>
<p>
    The <b>Eager Loading</b> existing to resolve the <code>N + 1</code> query problem.
    For example, in the <a href="/docs/databases/define-model">Defining Models</a>,
    the <code>Post</code> model related to <code>User</code> model specified by <code>Creator</code>.
    See an example above, we retrieving the creator name of post collection:
</p>.

<textarea class="code">
$posts = Posts::all();

foreach ($posts as $post) {
    echo $post->Creator->name;
}
</textarea>

<p>
    Assume the posts table have <code>100</code> records.<br>
    Normally, you are guessing there are total <code>101</code> queries executed.
    <code>1</code> is for the posts list, and <code>100</code> queries for each post to retrieving the creator name.<br>
    Actually, there are just <code>2</code> queries executed:
</p>
<textarea class="code" data-type="sql">
SELECT id, creator_id, title, created_time, modified_time FROM posts;
SELECT id, name WHERE (id IN(/* the creator_id FROM post list*/))
</textarea>
<br>

<div class="alert alert-info">
Unlike some ORM libraries, they implemented <b>Eager Loading</b> by style look like this:
<textarea class="code">
$posts = Post::with('Creator');

foreach ($posts as $post) {
    echo $post->Creator->name;
}
</textarea>
<b>Lazy ORM</b> do this automatically. No need to call something like <code>with('Creator')</code>
</div>