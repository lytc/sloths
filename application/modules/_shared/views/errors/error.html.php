<h2 class="text-danger"><i class="fa fa-warning"></i> <?= $exception->getCode() ?> <?= $exception->getMessage() ?></h2>
<hr>

<? if ($this->app()->getDebug()): ?>
<pre><b><?= $exception->getFile() ?>(<?= $exception->getLine() ?>)</b>
<?= $exception->getTraceAsString() ?></pre>
<? endif ?>