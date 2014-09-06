<ul class="breadcrumb">
    <li><a href="<?= $this->url('') ?>"><i class="fa fa-home"></i> <?= $this->_('Home') ?></a></li>
    <?php foreach ($items as $title => $url): ?>
        <? if (is_numeric($title)): ?>
            <li class="current"><?= $this->e($url) ?></li>
        <? else: ?>
            <li><a href="<?= $this->url()->to($url) ?>"><?= $this->escape($title) ?></a></li>
        <? endif ?>
    <? endforeach ?>
</ul>