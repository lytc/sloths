<div class="page-header">
    <h1>ORM</h1>
</div>

<h3>Defining ORM Model and Relationships</h3>
<code>models\User.php</code>
<textarea class="code">
use Lazy\Db\Model\Model;

class User extend Model
{
    // protected static $tableName = 'users' // by default
    // protected static $primaryKey = 'id' // by default

    protected static $columns = [
        'id'        => self::INT,
        'group_id'  => self::INT,
        'name'      => self::VARCHAR,
        'email'     => self::VARCHAR,
        'password'  => self::VARCHAR,
        'status'    => self::VARCHAR
    ];

    protected static $hasOne            = ['Profile'];
    protected static $belongsTo         = ['Group'];
    protected static $hasMany           = ['Posts'];
    protected static $hasManyThrough    = [
        'Roles' => ['model' => 'Roles', 'throughModel' => 'UserRole']
    ];
}
</textarea>
<br>

<code>models\Profile.php</code>
<textarea class="code">
class Profile extend Model
{
    protected static $primaryKey = 'user_id';

    protected static $columns = [
        'user_id'       => self::INT,
        'resume'        => self::TEXT,
        'modified_time' => self::DATETIME
    ];

    protected static $belongsTo = ['User'];
}
</textarea>
<br>

<code>models\Group.php</code>
<textarea class="code">
class Group extend Model
{
    protected static $columns = [
        'id'    => self::INT,
        'name'  => self::VARCHAR
    ];

    protected static $hasMany = ['Users'];
}
</textarea>
<br>

<code>models\Role.php</code>
<textarea class="code">
class Role extend Model
{
    protected static $columns = [
        'id'    => self::INT,
        'name'  => self::VARCHAR
    ];

    protected static $hasManyThrough = [
        'Users' => ['model' => 'User', 'throughModel' => 'UserRole']
    ];
}
</textarea>
<br>

<code>models\UserRole.php</code>
<textarea class="code">
class UserRole extend Model
{
    protected static $columns = [
        'user_id'   => self::INT,
        'role_id'   => self::INT
    ];
}
</textarea>
<br>

<code>models\Posts.php</code>
<textarea class="code">
class Post extend Model
{
    protected static $columns = [
        'id'            => self::INT,
        'creator_id'    => self::INT,
        'title'         => self::VARCHAR,
        'content'       => self::TEXT,
        'created_time'  => self::DATETIME,
        'modified_time' => self::DATETIME
    ];

    protected static $belongsTo = ['Users'];
}
</textarea>