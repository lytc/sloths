<? $this->breadcrumb(['Profile' => $this->url('profile'), 'Edit']) ?>

<? $user = $this->user() ?>

<form data-ajax action="<?= $this->url() ?>" data-method="PUT" method="POST" class="form-horizontal">
    <div class="row">
        <div class="col-sm-3">
            <div class="thumbnail">
                <img src="<?= $user->avatar ?>">
            </div>
            <button type="button" class="btn btn-primary btn-block">Change</button>
        </div>
        <div class="col-sm-9">
            <fieldset>
                <legend>Sign In Information</legend>
                <div class="form-group">
                    <label class="col-sm-3 control-label">Email</label>
                    <div class="col-sm-9">
                        <div class="form-control" disabled><?= $this->e($user->email) ?></div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label required">Current password</label>
                    <div class="col-sm-9">
                        <input type="password" name="currentPassword" required autofocus class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">New password</label>
                    <div class="col-sm-9">
                        <input type="password" name="password" class="form-control">
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <legend>Personal Information</legend>
                <div class="form-group">
                    <label class="col-sm-3 control-label required">Name</label>
                    <div class="col-sm-9">
                        <input type="text" name="name" required class="form-control" value="<?= $this->e($user->name) ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">Phone</label>
                    <div class="col-sm-9">
                        <input type="text" name="phone" class="form-control" value="<?= $this->e($user->phone) ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">Address</label>
                    <div class="col-sm-9">
                        <input type="text" name="address" class="form-control" value="<?= $this->e($user->address) ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">Birthday</label>
                    <div class="col-sm-9">
                        <input type="date" name="birthday" class="form-control" value="<?= $this->e($user->birthday) ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"></label>
                    <div class="col-sm-9">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</form>