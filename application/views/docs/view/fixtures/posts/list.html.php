    <ul>
        <? foreach ($posts as $post): ?>
        <li><?= $post['title'] ?></li>
        <? endforeach ?>
    </ul>
    <?= $this->paginate($posts) ?>