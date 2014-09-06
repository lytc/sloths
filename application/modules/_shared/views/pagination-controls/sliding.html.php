<ul class="pagination">

    <?php if ($paginator->getCurrentPage() > 1): ?>
        <li><a href="<?= $this->url()->current(['page' => 1]) ?>">««</a></li>
    <?php else: ?>
        <li class="disabled"><span>««</span></li>
    <?php endif ?>

    <?php if ($prevPageNumber = $paginator->getPrevPageNumber()): ?>
        <li><a href="<?= $this->url()->current(['page' => $prevPageNumber]) ?>">«</a></li>
    <?php else: ?>
        <li class="disabled"><span>«</span></li>
    <?php endif ?>

    <?php for ($i = $paginator->getFirstPageInRange(); $i <= $paginator->getLastPageInRange(); $i++): ?>
        <li<?php if ($i == $paginator->getCurrentPage()): ?> class="active"<?php endif ?>><a href="<?= $this->url()->current(['page' => $i]) ?>"><?= $i ?></a></li>
    <?php endfor ?>

    <?php if ($nextPageNumber = $paginator->getNextPageNumber()): ?>
        <li><a href="<?= $this->url()->current(['page' => $nextPageNumber]) ?>">»</a></li>
    <?php else: ?>
        <li class="disabled"><span>»</span></li>
    <?php endif ?>

    <?php if ($paginator->getTotalPages() && $paginator->getCurrentPage() != $paginator->getTotalPages()): ?>
        <li><a href="<?= $this->url()->current(['page' => $paginator->getTotalPages()]) ?>">»»</a></li>
    <?php else: ?>
        <li class="disabled"><span>»»</span></li>
    <?php endif ?>
</ul>