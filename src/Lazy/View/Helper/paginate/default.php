<ul class="pagination">
    <? if ($prevPageNumber = $paginator->getPrevPageNumber()): ?>
        <li><a href="<?= $paginate->url($prevPageNumber) ?>">«</a></li>
    <? else: ?>
        <li class="disabled"><span>«</span></li>
    <? endif ?>
    <? for ($i = $paginator->getFirstPageInRange(); $i <= $paginator->getLastPageInRange(); $i++): ?>
        <li><a href="<?= $paginate->url($i) ?>"><?= $i ?></a></li>
    <? endfor ?>
    <? if ($nextPageNumber = $paginator->getNextPageNumber()): ?>
        <li><a href="<?= $paginate->url($nextPageNumber) ?>">»</a></li>
    <? else: ?>
        <li class="disabled"><span>»</span></li>
    <? endif ?>
</ul>