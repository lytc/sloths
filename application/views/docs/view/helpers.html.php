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
    <p>For development mode, I'm recommend you set the disable caching param value is <code>time()</code></p>
</div>
<textarea class="code">
    \Sloths\View\Helper\AssetTag::setDefaultDisableCachingParam(time());
</textarea>
<textarea class="code">
    <?php \Sloths\View\Helper\AssetTag::setDefaultDisableCachingParam(time()); ?>
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
    &lt;?= $this->formatDateTime(<?= date('Y-m-d H:i:s') ?>) ?&gt;
    // Output: <?= $this->formatDateTime(date('Y-m-d H:i:s')) ?>


    &lt;?= $this->formatDate(<?= date('Y-m-d') ?>) ?&gt;
    // Output: <?= $this->formatDate(date('Y-m-d')) ?>


    &lt;?php \Sloths\View\Helper\FormatDateTime::setDefaultOutputFormat('c') ?&gt;
    &lt;?= $this->formatDateTime(date('Y-m-d H:i:s')) ?&gt;
    <?php \Sloths\View\Helper\FormatDateTime::setDefaultOutputFormat('c') ?>
// Output: <?= $this->formatDateTime(date('Y-m-d H:i:s')) ?>


    &lt;?= $this->formatDateTime(date('Y-m-d H:i:s'))->setOutputFormat('r') ?&gt;
    // Output: <?= $this->formatDateTime(date('Y-m-d H:i:s'))->setOutputFormat('r') ?>
</textarea>

<h4>MailTo Helper</h4>

<h4>Partial & PartialLoop Helper</h4>

<h4>Capture Helper</h4>

<h4>Url Helper</h4>

<h4>Paginate Helper</h4>