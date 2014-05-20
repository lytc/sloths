<h3>My View</h3>

<?= $this->partial('partial') ?>

<ul>
    <?= $this->partialLoop('item', $items) ?>
</ul>