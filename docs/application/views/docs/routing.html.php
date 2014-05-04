<div class="page-header">
    <h1>Routing</h1>
</div>

<blockquote>
    <p>Lazy Routes learned from <a href="http://www.sinatrarb.com/intro.html#Routes">Sinatra Routes</a>!</p>
</blockquote>

<div class="alert alert-info">
    The context of route callback handler is the called. Commonly, it is the <code>\Lazy\Application\Application</code>.
    So every properties, methods of the context is available within the callback handler.
</div>

<p>
    Routes are matched in the order they are defined.
    The first route that matches the request is invoked
    unless you call <a href="/docs/application#pass"><code>pass()</code></a> within the route callback, the process will be matching the next route.
</p>

<p>Route patterns can be static pattern:</p>
<textarea class="code">
    $this->get('/foo', function() {
        # matches "GET /foo" but not "GET /bar"
    });
</textarea>
<br>

<p>Or includes named parameters:</p>
<textarea class="code">
    $this->get('/hello/:name', function($name) {
        # matches "GET /hello/foo" and "GET /hello/bar"
        # $name is 'foo' or 'bar'
    });
</textarea>
<br>

<p>Or named parameters with double colon:</p>
<div><em>Named parameters with double colon only matches the numeric value.</em></div>
<textarea class="code">
    $this->get('/posts/::id', function($id) {
        # matches "GET /posts/123"
        # $id is 123
    });
</textarea>
<br>

<p>Or Includes wildcard parameters:</p>
<textarea class="code">
    $this->get('/say/*/to/*', function($splat) {
        # matches "GET /say/hello/to/world"
        $splat # => ['hello', 'world']
    });

    $this->get('/download/*.*', function($splat) {
        # matches "GET /download/path/to/file.zip"
        $splat # => ['path/to/file', 'zip']
    });
</textarea>
<br>

<p>Or With Regular Expressions: <em>(Regex pattern should start with <code>#</code> character)</em></p>
<textarea class="code">
    $this->get('#/hello/(\w+)', function($name) {
        # matches "GET /hello/foo"
        # $name is 'foo'
    };
</textarea>
<br>

<p>Route patterns may have optional parameters:</p>
<textarea class="code">
    $this->get('/posts.?:format?', function($format) {
        # matches "GET /posts" and any extension "GET /posts.xml", "GET /posts.json" etc.
        # $format is "null" or "xml" or "json"
    });
</textarea>

<h4>Access to request parameters:</h4>
<textarea class="code">
    $this->get('/posts', function() {
        # matches "GET /posts?author=foo"
        $author = $this->params->author; # => "foo"
        $author = $this->param('author'); # => "foo"
    });
</textarea>

<h4>Return Values</h4>
<p>Most commonly, this is a string. But other values are also accepted.</p>
<p>
    The return values will be send to response body. It mean if return values is an array or object,
    the response object will be send it as json string and the content type header is <code>application/json</code>.
</p>
<textarea class="code">
    $this->get('/posts?.format?', function($format) {
        $posts = Post::all();

        if ('json' == $format) {
            return $posts;
        }

        return $this->render('/posts/list', ['posts' => $posts]);
    });
</textarea>

<h4>Simple CRUD Routes</h4>
<code>application\routes\posts.php</code>
<textarea class="code">
    # GET /posts
    $this->get('/', function() {
    //...
    });

    # GET /posts/123
    $this->get('/::id', function($id) {
    //...
    });

    # POST /posts
    $this->post('/', function() {
    //...
    });

    # GET /posts/123/edit
    $this->get('/::id/edit', function($id) {

    });

    # PATCH /posts/123
    $this->patch('/::id', function($id) {
    //...
    });

    # DELETE /posts/123
    $this->delete('/::id', function($id) {
    //...
    });
</textarea>