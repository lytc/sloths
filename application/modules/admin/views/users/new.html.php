<? $this->breadcrumb([$this->_('Users') => 'users', $this->_('New')]) ?>

<form class="form-horizontal" data-ajax method="POST" action="<?= $this->url()->to('users') ?>">
    <div class="row">
        <div class="col-sm-3">
            <div class="thumbnail">
                <img src="http://placehold.it/200x200">
            </div>
            <button type="button" class="btn btn-primary btn-block"><?= $this->_('Change avatar') ?></button>
        </div>
        <div class="col-sm-9">
            <fieldset>
                <legend><?= $this->_('Sign In Information') ?></legend>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?= $this->_('Email') ?></label>
                    <div class="col-sm-9">
                        <input type="text" name="email" autofocus class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label required"><?= $this->_('Password') ?></label>
                    <div class="col-sm-9">
                        <input type="password" name="password" required class="form-control">
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <legend><?= $this->_('Personal Information') ?></legend>
                <div class="form-group">
                    <label class="col-sm-3 control-label required"><?= $this->_('Name') ?></label>
                    <div class="col-sm-9">
                        <input type="text" name="name" required class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?= $this->_('Phone number') ?></label>
                    <div class="col-sm-9">
                        <input type="text" name="phone" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?= $this->_('Address') ?></label>
                    <div class="col-sm-9">
                        <input type="text" name="address" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?= $this->_('Birthday') ?></label>
                    <div class="col-sm-9">
                        <input type="date" name="birthday" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"></label>
                    <div class="col-sm-9">
                        <button type="submit" class="btn btn-primary"><?= $this->_('Save') ?></button>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</form>