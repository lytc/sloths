<? $count = count($elements) ?>
<? foreach ($elements as $index => $element): ?>
    <? if (isset($element['link'])): ?>
        <a href="<?= $this->escape($element['link']) ?>"><?= $this->escape($element['label']) ?></a>
    <? else: ?>
        <?= $this->escape($element['label']) ?>
    <? endif ?>

    <? if ($index < $count - 1): ?>
        &gt;
    <? endif ?>
<? endforeach ?>