<div class="page-header">
    <h1>Has Many</h1>
</div>

<p>
    In the <a href="/docs/databases/define-model">Defining Models</a>.
    <code>User</code> model has many <code>Post</code>.
    So you can display the title of posts by example:
</p>
<textarea class="code">
$user = User::first(1);
foreach ($user->Posts as $post) {
    echo $post->title;
}
</textarea>
<br>

<p>
    Has Many property is an instance of <code>Lazy\Db\Model\Collection</code>.
    So you can add more filters to that collection before retrieving any data of that collection.
</p>
<textarea class="code">
$user = User::first(1);
$posts = $user->Posts;

$posts->where('created_time >= ?', '2014-05-03')
    ->orderBy('created_time DESC')
    ->limit(10);

foreach ($posts as $post) {
    echo $post->title;
}
</textarea>