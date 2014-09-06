<? $this->breadcrumb([$this->_('Posts') => 'posts', $this->_('New')]) ?>

<? $this->assets()->uses('posts') ?>

<form class="form-horizontal" data-ajax method="POST" action="<?= $this->url()->to('posts') ?>">
    <div class="form-group">
        <label class="col-md-2 col-sm-3 control-label"><?= $this->_('Title') ?></label>
        <div class="col-md-10 col-sm-9">
            <input type="text" name="title" autofocus class="form-control">
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 col-sm-3 control-label"><?= $this->_('Thumbnail') ?></label>
        <div class="col-md-10 col-sm-9">
            <div class="thumbnail">
                <img src="http://placehold.it/200x200">
            </div>
            <button type="button" class="btn btn-primary btn-block"><?= $this->_('Select an image') ?></button>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 col-sm-3 control-label required"><?= $this->_('Summary') ?></label>
        <div class="col-md-10 col-sm-9">
            <textarea name="summary" class="html-editor form-control"></textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 col-sm-3 control-label required"><?= $this->_('Content') ?></label>
        <div class="col-md-10 col-sm-9">
            <textarea name="content" class="html-editor form-control"></textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 col-sm-3 control-label"></label>
        <div class="col-md-10 col-sm-9">
            <button type="submit" class="btn btn-primary"><?= $this->_('Save') ?></button>
        </div>
    </div>
</form>