<? $this->breadcrumb([$this->_('Roles') => 'roles', $this->_('List')]) ?>

<div class="text-right">
    <a href="<?= $this->url()->add($roles) ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i></a>
</div>

<hr>

<table class="table grid">
    <thead>
    <tr>
        <th>#</th>
        <th><?= $this->_('Name') ?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <? foreach ($roles as $role): ?>
        <tr>
            <td><?= $role->id() ?></td>
            <td><?= $this->e($role->name) ?></td>
            <td>
               <a href="<?= $this->url()->edit($role) ?>" class="btn btn-xs btn-info" title="<?= $this->_('Edit') ?>"><i class="fa fa-edit"></i></a>
               <a href="<?= $this->url()->delete($role) ?>" data-action="DELETE" class="btn btn-xs btn-danger" title="<?= $this->_('Delete') ?>"><i class="fa fa-trash-o"></i></a>
            </td>
        </tr>
    <? endforeach ?>
    </tbody>
</table>