<div class="page-header">
    <h1>Model</h1>
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

<h4>Working with CRUD</h4>
<code>CREATE</code>
<textarea class="code">
$user = User::create([
    'name'  => 'Ly Tran',
    'email' => 'prtran@gmail.com',
]);

$user->save();
echo $user->id;
// or
$user = User::create();
$user->name = 'Ly Tran';
$user->email = 'prtran@gmail.com';
</textarea>
<br>

<code>READ</code>
<textarea class="code">
$user = User::first(1);

echo $user->name;
echo $user->email;
var_dump($user->toArray());
echo json_encode($user);
</textarea>
<br>

<code>UPDATE</code>
<textarea class="code">
$user = User::first(1);

$user->name = 'Ly Tran';
$user->email = 'prtran@gmail.com';

$user->save();

// mass assignment
$user->fromArray([
    'name'  => 'Ly Tran',
    'email' => 'prtran@gmail.com'
]);
$user->save();
</textarea>
<br>

<code>DELETE</code>
<textarea class="code">
$user = User::first(1);
$user->delete();
</textarea>