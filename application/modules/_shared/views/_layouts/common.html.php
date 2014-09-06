<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= isset($headTitle)? $headTitle : 'Sloths' ?></title>

    <?= $this->assets()->render('css') ?>

</head>
<body>

<?= $this->content() ?>

<?= $this->assets()->render('js') ?>

</body>
</html>