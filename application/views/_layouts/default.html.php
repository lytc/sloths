<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>
        <?= isset($_title)? $this->escape($_title) : 'Sloths Framework Documentation' ?>
    </title>
    <meta name="keywords" content="sloths, php, framework, orm, routing, mvc, web">
    <meta name="description" content="Sloths - The small PHP framework for lazy man.">
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="/sloths/assets/vendor/codemirror/4.1/lib/codemirror.css" rel="stylesheet">
    <link href="/sloths/assets/vendor/codemirror/4.1/theme/mdn-like.css" rel="stylesheet">
    <?= $this->stylesheetTag('/sloths/assets/stylesheets/application.css') ?>
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
            <a class="navbar-brand" href="/sloths/index.html">Sloths Framework</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="/sloths/docs/installation.html">Documentation</a></li>
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
                <iframe src="http://ghbtns.com/github-btn.html?user=lytc&repo=sloths&type=watch&count=true"
                        allowtransparency="true" frameborder="0" scrolling="0" width="100" height="20"></iframe>
                <iframe src="http://ghbtns.com/github-btn.html?user=lytc&repo=sloths&type=fork&count=true"
                        allowtransparency="true" frameborder="0" scrolling="0" width="100" height="20"></iframe>
                <iframe src="http://ghbtns.com/github-btn.html?user=lytc&repo=sloths&type=follow&count=true"
                        allowtransparency="true" frameborder="0" scrolling="0" width="180" height="20"></iframe>
            </div>
        </div>
        <div class="pull-right">
            <ul class="list-inline">
                <li><a href="https://github.com/lytc/sloths">GitHub</a></li>
                <li><a href="/sloths/docs/installation.html">Documentation</a></li>
            </ul>
        </div>
    </div>
</footer>
<br>
<br>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

<script src="/sloths/assets/vendor/codemirror/4.1/lib/codemirror.js"></script>
<script src="/sloths/assets/vendor/codemirror/4.1/lib/util/formatting.js"></script>
<script src="/sloths/assets/vendor/codemirror/4.1/addon/edit/matchbrackets.js"></script>
<script src="/sloths/assets/vendor/codemirror/4.1/mode/htmlmixed/htmlmixed.js"></script>
<script src="/sloths/assets/vendor/codemirror/4.1/mode/xml/xml.js"></script>
<script src="/sloths/assets/vendor/codemirror/4.1/mode/javascript/javascript.js"></script>
<script src="/sloths/assets/vendor/codemirror/4.1/mode/css/css.js"></script>
<script src="/sloths/assets/vendor/codemirror/4.1/mode/clike/clike.js"></script>
<script src="/sloths/assets/vendor/codemirror/4.1/mode/php/php.js"></script>
<script src="/sloths/assets/vendor/codemirror/4.1/mode/nginx/nginx.js"></script>
<script src="/sloths/assets/vendor/codemirror/4.1/mode/sql/sql.js"></script>
<?= $this->scriptTag('/sloths/assets/javascripts/application.js') ?>
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-50648420-1', 'lytc.github.io');
    ga('send', 'pageview');

</script>
</body>
</html>