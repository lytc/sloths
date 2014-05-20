<div class="page-header">
    <h1>Application Structure</h1>
</div>

<h3>Classic Style Application</h3>
<textarea class="code">
    require_once 'vendor/autoload.php';

    $application = new Sloths\Application\Application();

    $application->get('/', function() {
        //...
    });

    // more routes mapping here

    $application->run();
</textarea>

<h3>Single Module Application</h3>
<p>Normally, the application structure look like this:</p>
<pre>
\root-directory
    \application
        \config
            application.php
            view.php
        \models
        \routes
            index.php
        \views
            \helpers
        MyApplication.php
    \public
        index.php
    composer.json
</pre>
<code>public/index.php</code>
<textarea class="code">
    require_once 'vendor\autoload.php';

    $application = new MyApplication();
    $application->run();
</textarea>

<h3>Modular Application</h3>
<p>Normally, the application structure look like this:</p>
<pre>
\root-directory
    \application
        \config
            application.php
            view.php
        \models
        \modules
            \content
                \routes
                \views
                ContentApplication.php
            \admin
                \routes
                \views
                AdminApplication.php
    \public
        index.php
    composer.json
</pre>
<code>public/index.php</code>
<textarea class="code">
    require_once 'vendor\autoload.php';

    $request = new Sloths\Http\Request();

    if (preg_match('/^admin/', $request->getPath())) {
        $application = new AdminApplication('/admin');
    } else {
        $application = new ContentApplication();
    }

    $application->run();
</textarea>