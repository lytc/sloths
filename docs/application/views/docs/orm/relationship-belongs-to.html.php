<div class="page-header">
    <h1>Has One</h1>
</div>

<p>
    In the <a href="/docs/databases/define-model">Defining Models</a>.
    <code>Post</code> model belongs to <code>User</code> specified by <code>Creator</code>.
    So you can get the creator name of a post by an example:
</p>
<textarea class="code">
$post = Post::first(1);
echo $post->Creator->name;
</textarea>
<br>

<p>And, of course. You can do that through a collection</p>
<textarea class="code">
foreach (Post::all() as $post) {
    echo $post->Creator->name;
}
</textarea>
<br>

<div class="alert alert-info">
Don't worry about <code>N + 1</code> query problem in here. We resolve it for you.
See <a href="/docs/orm/collection#eager-loading">Eager Loading</a> for more details.
</div>