<? $this->breadcrumb([$this->_('Roles') => 'roles', $this->_('Edit')]) ?>

<form class="form-horizontal" data-ajax method="POST" action="<?= $this->url()->update($role) ?>">
    <input type="hidden" name="_method" value="PUT">
    <div class="form-group">
        <label class="col-sm-2 control-label required"><?= $this->_('Name') ?></label>
        <div class="col-sm-10">
            <input type="text" name="name" required autofocus class="form-control" value="<?= $this->e($role->name) ?>">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-10 col-sm-push-2">
            <button type="submit" class="btn btn-primary"><?= $this->_('Save') ?></button>
        </div>
    </div>
</form>