<div class="page-header">
    <h1>Request</h1>
</div>

<p>The request object is a wrapper of HTTP Request.
    Commonly, it is use to access to the HTTP Request variables:
    <code>$_SERVER</code>, <code>$_GET</code>, <code>$_POST</code>, <code>$_COOKIE</code> and <code>$_FILES</code>.</p>

<textarea class="code">
    // $_SERVER = ['HTTP_HOST' => 'mydomain.com']
    // $_GET = ['foo' = 'bar']
    // $_POST = ['bar' = 'baz']

    $request = new Request();

    $request->getHost(); // mydomain.com
    $request->params->foo; // = "bar"
    $request->params->bar; // = "baz"
    $request->get->foo; // = "bar"
    $request->post->bar; // = "baz"
    //....
</textarea>
<br>

<p>Inside application context, for example is in your route. You can access to request parameters like this:</p>
<code>routes/posts.php</code>
<textarea class="code">
    $this->get('/', function() {
        // $_GET = ['search' => 'foo']
        $this->params->search; // = "foo"
    });

    $this->posts('/', function() {
        $post = new Post();
        $post->title    = $this->post->title;
        $post->content  = $this->post->content;
        //...
    });
</textarea>
