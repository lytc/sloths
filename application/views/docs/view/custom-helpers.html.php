<div class="page-header">
    <h1>Custom View Helpers</h1>
</div>

<p>If the builtin view helpers dones't fit your work, you could define your own view help. Let take a look example below:</p>
<p>For simple, I going to define helper <code>formatFileSize</code></p>

<code>application/views/helpers/FormatFileSize.php</code>
<textarea class="code" data-auto-trim=0>
<?= file_get_contents(__DIR__ . '/../../helpers/FormatFileSize.php') ?>
</textarea>
<br>
Register helper namespace: <code>application/config/view.php</code>
<textarea class="code">
    \Sloths\View\View::addHelperNamespace('Application\View\Helper');
</textarea>
<br>

<p>Now in your view script:</p>
<textarea class="code">
    &lt;?= $this->formatFileSize(123456789) ?&gt; // output: <?= $this->formatFileSize(123456789) ?>
</textarea>