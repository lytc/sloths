<div class="page-header">
    <h1>Request</h1>
</div>

<p>The Response object use for building HTTP responses.</p>
<h4>Basic Response</h4>

<code>routes/my-route.php</code>
<textarea class="code">
    $this->get('/foo', function() {
        return 'Hello!';
    });
</textarea>

<p>As you see the code above, there are nothing related to response.
    But behind the scene, the application will get the return value from route callback (<code>"Hello!"</code> in this case)and pass it to the response object.
    <code>$this->response->setBody("Hello!")</code>
</p>

<p>By default, the response status code is <code>200</code>.
    If you want to custom the response code, you can call <code>$this->response->setStatusCode(403)</code></p>

<p>Or you can add a custom response header: <code>$this->response->setHeader('header-name', 'foo')</code></p>

<h4>Response JSON data</h4>
<p>By default, if the response body not a string. <code>Array</code> or instance of <code>Model</code> for example.
The response object will encode the response body to JSON data, and set the response content type to <code>application/json</code>
</p>

<textarea class="code">
    $this->get('/posts', function() {
        return ['foo' => 'bar'];
    });
    // the response is: {"foo": "bar"}
</textarea>