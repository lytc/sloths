<? $this->breadcrumb([$this->_('Posts') => 'posts', $this->_('List')]) ?>

<div class="row">
    <div class="col-sm-6">
        <form method="GET" action="<?= $this->url() ?>">
            <input type="search" name="q" class="form-control" placeholder="<?= $this->_('Search') ?>..." value="<?= $this->e($this->params('q')) ?>">
        </form>
    </div>
    <div class="col-sm-6">
        <div class="text-right">
            <a href="<?= $this->url()->add($paginator->getIterator()) ?>" class="btn btn-primary btn-sm" title="<?= $this->_('Add') ?>"><i class="fa fa-plus"></i></a>
        </div>
    </div>
</div>

<hr>

<table class="table grid">
    <thead>
    <tr>
        <th>#</th>
        <th><?= $this->_('Title') ?></th>
        <th><?= $this->_('Created time') ?></th>
        <th><?= $this->_('Creator') ?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <? foreach ($paginator as $post): ?>
        <tr>
            <td><?= $post->id() ?></td>
            <td><?= $this->e($post->title) ?></td>
            <td><?= $this->formatDateTime($post->createdTime) ?></td>
            <td><a href="<?= $this->url()->view($post->Creator) ?>"><?= $this->e($post->Creator->name) ?></a></td>
            <td>
               <a href="<?= $this->url()->view($post) ?>" class="btn btn-xs btn-info" title="<?= $this->_('View') ?>"><i class="fa fa-file"></i></a>
               <a href="<?= $this->url()->edit($post) ?>" class="btn btn-xs btn-info" title="<?= $this->_('Edit') ?>"><i class="fa fa-edit"></i></a>
               <a href="<?= $this->url()->delete($post) ?>" data-action="DELETE" class="btn btn-xs btn-danger" title="<?= $this->_('Delete') ?>"><i class="fa fa-trash-o"></i></a>
            </td>
        </tr>
    <? endforeach ?>
    </tbody>
</table>

<?= $this->paginate($paginator) ?>