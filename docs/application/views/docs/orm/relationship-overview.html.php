<div class="page-header">
    <h1>Relationships</h1>
</div>

<p>Lazy Model supports many types of relationships:</p>
<ul>
    <li><a href="#">One to One</a></li>
    <li><a href="#">One to Many</a></li>
    <li><a href="#">Many to Many</a></li>
</ul>

<p>Assume we have models defined in <a href="/docs/databases/define-model">Defining Models</a></p>

<h4>One to One <small><code>$hasOne</code></small></h4>
<textarea class="code">
// Retrieving user profile
$user = User::first(1);
echo $user->Profile->resume;
</textarea>

<h4>One to Many <small><code>$hasMany</code> and <code>$belongsTo</code></small></h4>
<textarea class="code">
// list all posts of user with id is 1
$user = User::first(1);
$posts = $user->Posts;

// get creator name of a post with id is 1
$post = Post::first(1);
$post->Creator->name;
</textarea>

<h4>Many to Many <code>$hasManyThrough</code></small></h4>
<textarea class="code">
// list all roles of a user with id is 1
$user = User::first(1);
$user->Roles;

// list all users of a role with id is 1
$role = Role::first(1);
$role->Users;
</textarea>