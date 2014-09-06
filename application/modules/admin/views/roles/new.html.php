<? $this->breadcrumb([$this->_('Roles') => 'roles', $this->_('New')]) ?>

<form class="form-horizontal" data-ajax method="POST" action="<?= $this->url()->to('roles') ?>">
    <div class="form-group">
        <label class="col-sm-2 control-label required"><?= $this->_('Name') ?></label>
        <div class="col-sm-10">
            <input type="text" name="name" required autofocus class="form-control">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-10 col-sm-push-2">
            <button type="submit" class="btn btn-primary"><?= $this->_('Save') ?></button>
        </div>
    </div>
</form>