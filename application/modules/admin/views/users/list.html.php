<? $this->breadcrumb([$this->_('Users') => 'users', $this->_('List')]) ?>

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
        <th><?= $this->_('Name') ?></th>
        <th>Email</th>
        <th><?= $this->_('Phone number') ?></th>
        <th><?= $this->_('Created time') ?></th>
        <th><?= $this->_('Role') ?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <? foreach ($paginator as $user): ?>
        <tr>
            <td><?= $user->id() ?></td>
            <td><?= $this->e($user->name) ?></td>
            <td><?= $this->e($user->email) ?></td>
            <td><?= $this->e($user->phone) ?></td>
            <td><?= $this->formatDateTime($user->createdTime) ?></td>
            <td>
                <?= implode(', ', $user->Roles->remember('+10 seconds')->column('name')) ?>
            </td>
            <td>
               <a href="<?= $this->url()->view($user) ?>" class="btn btn-xs btn-info" title="<?= $this->_('View') ?>"><i class="fa fa-file"></i></a>
               <a href="<?= $this->url()->edit($user) ?>" class="btn btn-xs btn-info" title="<?= $this->_('Edit') ?>"><i class="fa fa-edit"></i></a>
               <a href="<?= $this->url()->delete($user) ?>" data-action="DELETE" class="btn btn-xs btn-danger" title="<?= $this->_('Delete') ?>"><i class="fa fa-trash-o"></i></a>
            </td>
        </tr>
    <? endforeach ?>
    </tbody>
</table>

<?= $this->paginate($paginator) ?>