<div class="page-header">
    <h1>View</h1>
</div>

<p>The View exists to keep the view script separate from the model & route script.
    It provides a system of helpers, filters, variables and layout manager.</p>

<p>Commonly, your route script creates an instance of View and assign variables to that instance.
    The route script will return output string from view by call <code>render</code>.</p>

<code>routes/posts.php</code>
<textarea class="code">
    $this->get('/::id', function($id) {
        $post = Post::first($id);
        $post || $this->notFound();

        return $this->render('posts/view', [
            'post' => $post
        ]);
    });
</textarea>
<br>

<code>views/posts/view.html.php</code>
<textarea class="code" data-type="application/x-httpd-php">
    <h3>&lt;?= $this->escape($post->title) ?&gt;</h3>
    &lt;?= $post->content ?&gt;
</textarea>
<br>

<h4>View Script Directory</h4>
<p>Usually, you would like to set the directory of view scripts, it make the view script name is shorter when calling the <code>render</code>,
and make less effort when moving view scripts to another directory. You can change the default directory of all view script by calling <code>setDirectory</code> method.
</p>
<p>By default, the view script directory is <code>your-application-path/views</code>.
    If you call <code>render</code> with relative script path (<code>render('posts/view')</code>), the actual script file will be <code>your-application-path/views/posts.view.html.php</code>.<br>
    But if you all <code>render</code> with absolute script path (start with <code>/</code>), the view directory is ignore. <code>render('/posts/view')</code>,
    the actual view script path will be <code>/posts/view.html.php</code>
</p>

<h4>View Script Extension</h4>
<p>The view script extension is optional when calling <code>render</code>.
    It will assume the extension is <code>.html.php</code> by default,
    but you can changes to whatever you like by calling <code>setExtension</code> method or just simple calling <code>render</code> with view script and extension.</p>

<textarea class="code">
    $this->view->setExtension('phtml');
    $this->render('posts/view');

    // or
    $this->render('posts/view.phtml');
</textarea>
<br>

<h4>Passing Variables To View Script</h4>
<p>Passing variables to view script by calling <code>setVar</code>, <code>setVars</code> or <code>addVars</code></p>
<textarea class="code">
    $this->view->setVar('foo', 'foo');
    // view variables is: ['foo' => 'foo', 'bar' => 'bar']

    $this->view->setVars(['foo' => 'bar', 'baz' => 'baz']);
    // view variables is: ['foo' => 'bar', 'baz' => 'baz'], the variables was reset and re assign

    $this->view->addVars(['baz' => 'qux', 'qux' => 'qux']);
    // view variables is: ['foo' => 'bar', 'baz' => 'qux', 'qux' => 'qux']
</textarea>
<br>
<p>Or simply, just passing the variables to the seconds arguments when calling <code>render</code></p>
<textarea class="code">
    $this->render('posts/view', [
        'post' => $post
    ]);

    // view variables is ['post' => $post]
</textarea>
<h4>View and Layouts</h4>
<p>The layout manager is very helpful. For example, when you want all your view scripts wrapped by the header and footer.
    You could define the header and footer in separate script called <code>layout</code>, and the body content is defined in a separate view script file.</p>

<code>views/_layouts/default.html.php</code>
<textarea class="code" data-type="application/x-httpd-php">
    <header>My Header</header>

    &lt;?= $this->content() ?&gt;

    <footer>My Footer</footer>
</textarea>
<br>
<code>views/posts/view.html.php</code>
<textarea class="code" data-type="application/x-httpd-php">
    <h3>&lt;?= $this->escape($post->title) ?&gt;</h3>
    <p>&lt;?= $post->content ?&gt;</p>
</textarea>
<br>
<code>routes/posts.php</code>
<textarea class="code">
    $this->get('/::id', function($id) {
        $post = Post::first($id);

        $this->setLayout('default'); // is the same of $this->view->setLayout('default')

        return $this->render('posts/view', ['post' => $post]);
    });
</textarea>
<br>
The output is:
<textarea class="code" data-type="application/x-httpd-php">
    <header>My Header</header>
    <h3>Post title</h3>
    <p>Post content</p>
    <footer>My Footer</footer>
</textarea>
<br>
<div class="alert alert-info">
    <p>If you set config the layout and want to disable it in some case, like fetching script content without wrap layout, just call <code>setLayout(false)</code></p>
</div>
<h4>Where you can config the layout?</h4><br>
<p>You can config the layout in some places:</p>
<code>1. View config file: application/config/view.php</code>
<textarea class="code">
    $this->setLayout('default');
</textarea>
<br>

<code>2. Within your route script or route file: routes/posts.php</code>
<textarea class="code">
    $this->setLayout('default');

    $this->get('/', function() {
        // or
        $this->setLayout('default');

        //....
    })
</textarea>
<br>

<code>3. Or inside your view script: views/posts/view.html.php</code>
<textarea class="code" data-type="application/x-httpd-php">
    &lt;?php $this->setLayout('default') ?&gt;

    &lt;?= $this->escape($post->title) ?&gt;
    &lt;?= $post->content ?&gt;
</textarea>

<h4>Nested layout</h4>
<p>It is very simple to setting up the nested layouts by calling <code>setLayout()</code> inside a layout. By this way, you can structure nested layout.</p>
<p>For example, you have the layout (<code>default</code>) with footer and header like above,
    now you want to have a layout (<code>sidebar</code>) with left sidebar, the <code>sidebar</code> layout extends <code>default</code> layout.</p>
<code>views/_layouts/sidebar.html.php</code>
<textarea class="code">
    &lt;php $this->setLayout('default') ?&gt;
    <nav class="left-sidebar">
        Left Sidebar
    </nav>
    <div class="content-container">
        &lt;?= $this->content() ?%gt;
    </div>
</textarea>
<br>
<p>And now you want to render <code>views/posts/view.html.php</code> with <code>sidebar</code> layout</p>
<code>routes/posts.php</code>
<textarea class="code">
    $this->get('/::id', function($id) {
        $post = Post::first($id);

        $this->setLayout('sidebar');
        return $this->render('posts/view', ['post' => $post]);
    });
</textarea>
<br>

<p>The output string now is:</p>
<textarea class="code" data-type="application/x-httpd-php">
    <header>My Header</header>
    <nav class="left-sidebar">
        Left Sidebar
    </nav>
    <div class="content-container">
        <h3>Post title</h3>
        <p>Post content</p>
    </div>
    <footer>My Footer</footer>
</textarea>