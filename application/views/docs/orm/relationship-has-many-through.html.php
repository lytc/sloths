<div class="page-header">
    <h1>Has Many Through</h1>
</div>

<p>
    In the <a href="/docs/databases/define-model">Defining Models</a>.
    <code>User</code> model has many <code>Role</code>,
    and <code>Role</code> has many <code>User</code> too.
    So you can get the role list of a user, or user list of a role by an example:
</p>
<textarea class="code">
    $user = User::first(1);
    foreach ($user->Roles as $role) {
        echo $role->name;
    }

    $role = Role::first(1);
    foreach ($role->Users as $user) {
        echo $user->name;
    }
</textarea>

<p>And, of course. You can do that through collection</p>
<textarea class="code">
    foreach (User::all() as $user) {
        foreach ($user->Roles as $role) {
            echo $role->name;
        }
    }
</textarea>
<br>

<div class="alert alert-info">
    Don't worry about <code>N + 1</code> query problem in here. We resolve it for you.
    See <a href="/docs/orm/collection#eager-loading">Eager Loading</a> for more details.
</div>