<? $this->setLayout('default') ?>
<div class="jumbotron">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-push-3 col-sm-8 col-sm-push-2">
                <div class="welcome-code">
                    <? highlight_string('<?php
$this->get(\'/hi\', function() {
    return \'Hi Lazy Man, welcome to join us!\';
});'); ?>
                </div>
            </div>
        </div>
        <div class="text-center">
            <h1><span class="text-success">Lazy Framework</span></h1>
            <h3><span class="text-danger">PHP Framework for Lazy Man</span></h3>
            <br>
            <div>
                <iframe src="http://ghbtns.com/github-btn.html?user=lytc&repo=sloths&type=watch&count=true&size=large"
                        allowtransparency="true" frameborder="0" scrolling="0" width="150" height="30"></iframe>
                <iframe src="http://ghbtns.com/github-btn.html?user=lytc&repo=sloths&type=fork&count=true&size=large"
                        allowtransparency="true" frameborder="0" scrolling="0" width="150" height="30"></iframe>
                <iframe src="http://ghbtns.com/github-btn.html?user=lytc&repo=sloths&type=follow&count=true&size=large"
                        allowtransparency="true" frameborder="0" scrolling="0" width="240" height="30"></iframe>
            </div>
            <div>
                <a href="https://travis-ci.org/lytc/sloths"><img src="https://travis-ci.org/lytc/sloths.png"></a>
                <a href="https://coveralls.io/r/lytc/sloths"><img src="https://coveralls.io/repos/lytc/sloths/badge.png?branch=develop"></a>
                <a href="https://packagist.org/packages/lytc/sloths"><img src="https://poser.pugx.org/lytc/sloths/v/stable.png"></a>
                <a href="https://packagist.org/packages/lytc/sloths"><img src="https://poser.pugx.org/lytc/sloths/v/unstable.png"></a>
                <a href="https://packagist.org/packages/lytc/sloths"><img src="https://poser.pugx.org/lytc/sloths/downloads.png"></a>
            </div>
        </div>
    </div>
</div>

<div class="container">
</div>