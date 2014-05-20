<html>
        <head>
            <title><?= $this->capture('title') ?></title>
            <?= $this->capture('stylesheets') ?>

            <?= $this->capture('javascripts')->setRenderer([$this, 'scriptTag']) ?>

        </head>
        <body>
            <?= $this->content() ?>

        </body>
    </html>