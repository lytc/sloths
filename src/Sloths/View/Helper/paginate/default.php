<ul class="pagination">
    <?php if ($prevPageNumber = $paginator->getPrevPageNumber()): ?>
        <li><a href="<?= $paginate->url($prevPageNumber) ?>">«</a></li>
    <?php else: ?>
        <li class="disabled"><span>«</span></li>
    <?php endif ?>
    <?php for ($i = $paginator->getFirstPageInRange(); $i <= $paginator->getLastPageInRange(); $i++): ?>
        <li<?php if ($i == $paginator->getCurrentPage()): ?> class="active"<?php endif ?>><a href="<?= $paginate->url($i) ?>"><?= $i ?></a></li>
    <?php endfor ?>
    <?php if ($nextPageNumber = $paginator->getNextPageNumber()): ?>
        <li><a href="<?= $paginate->url($nextPageNumber) ?>">»</a></li>
    <?php else: ?>
        <li class="disabled"><span>»</span></li>
    <?php endif ?>
</ul>