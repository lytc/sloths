<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>
        <?= isset($_title)? $this->escape($_title) : 'Lazy Framework' ?>
    </title>
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="/lazy/assets/vendor/codemirror/4.1/lib/codemirror.css" rel="stylesheet">
    <link href="/lazy/assets/vendor/codemirror/4.1/theme/mdn-like.css" rel="stylesheet">
    <?= $this->stylesheetTag('/lazy/assets/stylesheets/application.css') ?>
    <?= $this->capture('stylesheets')->render([$this, 'stylesheetTag']) ?>
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/lazy/index.html">Lazy Framework</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="/lazy/docs/installation.html">Documentation</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>

<?= $this->content() ?>

<hr>

<footer>
    <div class="container">
        <div>
            <div class="pull-left">
                <iframe src="http://ghbtns.com/github-btn.html?user=lytc&repo=lazy&type=watch&count=true"
                        allowtransparency="true" frameborder="0" scrolling="0" width="100" height="20"></iframe>
                <iframe src="http://ghbtns.com/github-btn.html?user=lytc&repo=lazy&type=fork&count=true"
                        allowtransparency="true" frameborder="0" scrolling="0" width="100" height="20"></iframe>
                <iframe src="http://ghbtns.com/github-btn.html?user=lytc&repo=lazy&type=follow&count=true"
                        allowtransparency="true" frameborder="0" scrolling="0" width="100" height="20"></iframe>
            </div>
        </div>
        <div class="pull-right">
            <ul class="list-inline">
                <li><a href="https://github.com/lytc/lazy">GitHub</a></li>
                <li><a href="/docs">Documentation</a></li>
            </ul>
        </div>
    </div>
</footer>
<br>
<br>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

<script src="/lazy/assets/vendor/codemirror/4.1/lib/codemirror.js"></script>
<script src="/lazy/assets/vendor/codemirror/4.1/lib/util/formatting.js"></script>
<script src="/lazy/assets/vendor/codemirror/4.1/addon/edit/matchbrackets.js"></script>
<script src="/lazy/assets/vendor/codemirror/4.1/mode/htmlmixed/htmlmixed.js"></script>
<script src="/lazy/assets/vendor/codemirror/4.1/mode/javascript/javascript.js"></script>
<script src="/lazy/assets/vendor/codemirror/4.1/mode/css/css.js"></script>
<script src="/lazy/assets/vendor/codemirror/4.1/mode/clike/clike.js"></script>
<script src="/lazy/assets/vendor/codemirror/4.1/mode/php/php.js"></script>
<script src="/lazy/assets/vendor/codemirror/4.1/mode/nginx/nginx.js"></script>
<script src="/lazy/assets/vendor/codemirror/4.1/mode/sql/sql.js"></script>
<?= $this->scriptTag('/lazy/assets/javascripts/application.js') ?>
</body>
</html>