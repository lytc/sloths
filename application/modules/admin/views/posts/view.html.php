<? $this->breadcrumb([$this->_('Posts') => 'posts', $this->e($post->title)]) ?>

<table class="table">
    <tr>
        <td>Title</td>
        <td><?= $this->e($post->title) ?></td>
    </tr>
    <tr>
        <td>Author</td>
        <td><?= $this->e($post->Creator->name) ?></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
    </tr>
</table>

