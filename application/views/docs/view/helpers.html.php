<div class="page-header">
    <h1>View Helpers</h1>
</div>

<p><b>Sloths</b> shipping with a lot of builtin useful helpers</p>

<h4>HTML Assets Tag Helpers</h4>
<code>views/my-view-script.html.php</code>
<textarea class="code" data-type="application/x-httpd-php">
    &lt;?= $this->scriptTag('/assets/javascripts/application.js') ?&gt;
    &lt;?= $this->stylesheetTag('/assets/stylesheets/application.css') ?&gt;
    &lt;?= $this->imageTag('/assets/images/pic.jpg') ?&gt;
</textarea>
<br>
The output is:
<textarea class="code">
    <?= $this->scriptTag('/assets/javascripts/application.js') ?>

    <?= $this->stylesheetTag('/assets/stylesheets/application.css') ?>

    <?= $this->imageTag('/assets/images/pic.jpg') ?>
</textarea>
<br>

<h4>Disable Caching Assets Tag</h4>
<p>As you know, the assets tag might caching by the browser.
    For example, sometime you changes the javascript code,
    but you might need press <code>Ctrl+F5</code> on Windows or <code>Cmd+F5</code> on Mac to tell browser update your script code.
    Is is bad for end user, they don't know you was changes the script code.
    Solved that problem is very simple, just append the assert script source url with a increment parameter version number whenever you change the script code.
</p>

Commonly, I'm do that by this way:
<code>application/config/view.php</code>
<textarea class="code">
    \Sloths\View\Helper\AssetTag::setDefaultDisableCachingParam('v1'); // for all asset tags
    \Sloths\View\Helper\ScriptTag::setDefaultDisableCachingParam('v1'); // for all javascript tags
    \Sloths\View\Helper\StylesheetTag::setDefaultDisableCachingParam('v1'); // for all stylesheet tags
    \Sloths\View\Helper\ImageTag::setDefaultDisableCachingParam('v1'); // for all images tags
</textarea>
<p>Changes <code>v1</code> to <code>v2</code>, <code>v3</code>,.. whenever you change the script code.</p>
<?php \Sloths\View\Helper\AssetTag::setDefaultDisableCachingParam('v1'); ?>
<p>And now you call asset tag helper like above, the output is:</p>
<textarea class="code">
    <?= $this->scriptTag('/assets/javascripts/application.js') ?>

    <?= $this->stylesheetTag('/assets/stylesheets/application.css') ?>

    <?= $this->imageTag('/assets/images/pic.jpg') ?>
</textarea>
<p>Or if you want to directive config per assert by calling: <code>setDisableCachingParam('v1')</code></p>
<textarea class="code">
    &lt;?= $this->scriptTag('/assets/javascripts/application.js')->setDisableCachingParam('v2') ?&gt;
    &lt;?= $this->stylesheetTag('/assets/stylesheets/application.css')->setDisableCachingParam('v3') ?&gt;
    &lt;?= $this->imageTag('/assets/images/pic.jpg')->setDisableCachingParam('v4') ?&gt;
</textarea>
<br>

<p>The output now is:</p>
<textarea class="code">
    <?= $this->scriptTag('/assets/javascripts/application.js')->setDisableCachingParam('v2') ?>

    <?= $this->stylesheetTag('/assets/stylesheets/application.css')->setDisableCachingParam('v3') ?>

    <?= $this->imageTag('/assets/images/pic.jpg')->setDisableCachingParam('v4') ?>
</textarea>
<br>

<div class="alert alert-danger">
    <p>For development mode, I recommend you to set the disable caching param value to <code>time()</code></p>
</div>
<textarea class="code">
    \Sloths\View\Helper\AssetTag::setDefaultDisableCachingParam(time());
</textarea>
<br>

<p>The output look like:</p>
<textarea class="code">
    <?= $this->scriptTag('/assets/javascripts/application.js') ?>

    <?= $this->stylesheetTag('/assets/stylesheets/application.css') ?>

    <?= $this->imageTag('/assets/images/pic.jpg') ?>
</textarea>

<h4>Escape Helper</h4>
<code>views/my-view-script.php</code>
<textarea class="code" data-type="application/x-httpd-php">
    &lt;?= $this->escape('<div>'); ?&gt;
</textarea>
<p>output: <code><?= $this->escape('&lt;div&gt;') ?></code></p>

<h4>FormatDateTime & FormatDate Helper</h4>
<textarea class="code">
    &lt;?= $this->formatDateTime('<?= date('Y-m-d H:i:s') ?>') ?&gt;
    // Output: <?= $this->formatDateTime(date('Y-m-d H:i:s')) ?>


    &lt;?= $this->formatDate('<?= date('Y-m-d') ?>') ?&gt;
    // Output: <?= $this->formatDate(date('Y-m-d')) ?>


    &lt;?php \Sloths\View\Helper\FormatDateTime::setDefaultOutputFormat('c') ?&gt;
    &lt;?= $this->formatDateTime(date('Y-m-d H:i:s')) ?&gt;
    <?php \Sloths\View\Helper\FormatDateTime::setDefaultOutputFormat('c') ?>
// Output: <?= $this->formatDateTime(date('Y-m-d H:i:s')) ?>


    &lt;?= $this->formatDateTime(date('Y-m-d H:i:s'))->setOutputFormat('r') ?&gt;
    // Output: <?= $this->formatDateTime(date('Y-m-d H:i:s'))->setOutputFormat('r') ?>
</textarea>

<h4>MailTo Helper</h4>
<textarea class="code">
    $this->mailTo('prtran@gmail.com');
    // output: <?= $this->mailTo('prtran@gmail.com') ?>


    $this->mailTo(['prtran@gmail.com' => 'Ly Tran'], ['cc' => 'foo@example.com', 'bcc' => ['bar@example.com'], 'subject' => 'Subject', 'body' => 'Body'])
    //output: <?= $this->mailTo(['prtran@gmail.com' => 'Ly Tran'], ['cc' => 'foo@example.com', 'bcc' => ['bar@example.com'], 'subject' => 'Subject', 'body' => 'Body']) ?>
</textarea>

<h4>Partial & PartialLoop Helper</h4>
<code>views/partial.html.php</code>
<textarea class="code" data-type="application/x-httpd-php">
    <?= file_get_contents(__DIR__ . '/fixtures/partial.html.php') ?>
</textarea>
<code>views/my-view-scripts.php</code>
<textarea class="code" data-type="application/x-httpd-php">
    <?= file_get_contents(__DIR__ . '/fixtures/my-view-script.html.php') ?>
</textarea>
<code>routes/my-route.php</code>
<textarea class="code">
    $this->get('/', function() {
        return $this->render('my-view-script', [
            'name' => 'Ly Tran',
            'items' => [
                ['name' => 'Foo'],
                ['name' => 'Bar']
            ]
        ]);
    });
</textarea>
Output:
<textarea class="code" data-type="application/x-httpd-php">
    <?php
        $view = new \Sloths\View\View();
        $view->setDirectory(__DIR__ . '/fixtures');
        echo $view->render('my-view-script', ['name' => 'Ly Tran', 'items' => [['name' => 'Foo'], ['name' => 'Bar']]]);
    ?>
</textarea>

<h4>Capture Helper</h4>
<code>views/_layouts/default.html.php</code>
<textarea class="code" data-type="application/x-httpd-php">
    <?= file_get_contents(__DIR__ . '/fixtures/_layouts/default.html.php') ?>
</textarea>
<code>views/content.html.php</code>
<textarea class="code" data-type="application/x-httpd-php">
    <?= file_get_contents(__DIR__ . '/fixtures/content.html.php') ?>
</textarea>
<code>routes/my-route.php</code>
<textarea class="code">
    $this->get('/', function() {
        $this->setLayout('default');
        return $this->render('content');
    });
</textarea>
Output:
<textarea class="code" data-type="application/x-httpd-php">
    <?php
        \Sloths\View\Helper\AssetTag::setDefaultDisableCachingParam(false);
        $view = new \Sloths\View\View();
        $view->setDirectory(__DIR__ . '/fixtures');
        $view->setLayout('default');
        echo $view->render('content');
    ?>
</textarea>

<h4>Url Helper</h4>
<textarea class="code">
<?php \Sloths\View\Helper\Url::setDefaultUrl('/foo/bar?foo=bar') ?>
    &lt;?php \Sloths\View\Helper\Url::setDefaultUrl('/foo/bar?foo=bar') ?&gt;

    &lt;?= $this->url(['search' => 'foo']) ?&gt; // <?= $this->url(['search' => 'foo']) ?>

    &lt;?= $this->url('/posts', ['search' => 'foo']) ?&gt; // <?= $this->url('/posts', ['search' => 'foo']) ?>
</textarea>

<h4>Paginate Helper</h4>
<code>$paginator = new \Sloths\Pagination\Paginator($adapter);</code>
<br>
<p>
    <code>$adapter</code> can be:
    <ul>
        <li><code>\Sloths\Pagination\DataAdapter\ArrayAdapter</code></li>
        <li><code>\Sloths\Pagination\DataAdapter\DbSelect</code></li>
        <li><code>\Sloths\Pagination\DataAdapter\ModelCollection</code></li>
        <li><code>Array</code></li>
        <li><code>\Sloths\Db\Sql\Select</code></li>
        <li><code>\Sloths\Db\Model\Collection</code></li>
    </ul>
</p>
<code>routes/my-route.php</code>
<textarea class="code">

    $this->get('/', function() {
        \Sloths\View\Helper\Url::setDefaultUrl('#');
        // In the real work, it should be:
        // \Sloths\View\Helper\Url::setDefaultUrl($this->request->getUrl());

        $posts = [];
        for ($i = 0; $i < 100; $i++) {
            $posts[] = ['title' => 'post ' . $i];
        }

        $paginator = new \Sloths\Pagination\Paginator($posts);
        $paginator->setItemsCountPerPage(5);
        $paginator->setCurrentPage(8);

        return $this->render('posts/list', ['posts' => $posts]);
    });
</textarea>
<code>views/posts/list.html.php</code>
<textarea class="code" data-type="application/x-httpd-php">
<?= file_get_contents(__DIR__ . '/fixtures/posts/list.html.php') ?>
</textarea>

Output:
<?php
    \Sloths\View\Helper\Url::setDefaultUrl('#');
    $posts = [];
    for ($i = 0; $i < 100; $i++) {
        $posts[] = ['title' => 'post ' . $i];
    }
    $paginator = new \Sloths\Pagination\Paginator($posts);
    $paginator->setItemsCountPerPage(5);
    $paginator->setCurrentPage(8);

    $view = new \Sloths\View\View();
    $view->setDirectory(__DIR__ . '/fixtures');
    echo $view->render('posts/list', ['posts' => $paginator]);
?>