<div class="page-header">
    <h1>Has One</h1>
</div>

<p>
    In the <a href="/docs/databases/define-model">Defining Models</a>.
    <code>User</code> model has one <code>Profile</code>.
    So you can get the user resume by an example:
</p>
<textarea class="code">
$user = User::first(1);
echo $user->Profile->resume;
</textarea>
