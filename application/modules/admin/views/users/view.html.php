<? $this->breadcrumb([$this->_('Users') => 'users', $this->_('View')]) ?>

<fieldset>
    <legend><?= $this->_('Sign In Information') ?></legend>
    <table class="table">
        <tr>
            <td style="width: 100px"><?= $this->_('Email') ?></td>
            <td><?= $this->e($user->email) ?></td>
        </tr>
        <tr>
            <td><?= $this->_('Roles') ?></td>
            <td>
                <? foreach ($user->Roles as $role): ?>
                <span class="label label-info"><?= $this->_($role->name) ?></span>
                <? endforeach ?>
            </td>
        </tr>
    </table>
</fieldset>

<fieldset>
    <legend><?= $this->_('Personal Information') ?></legend>
    <table class="table">
        <tr>
            <td style="width: 100px"><?= $this->_('Name') ?></td>
            <td><?= $this->e($user->name) ?></td>
        </tr>
        <tr>
            <td><?= $this->_('Phone number') ?></td>
            <td><?= $this->e($user->phone) ?></td>
        </tr>
        <tr>
            <td><?= $this->_('Address') ?></td>
            <td><?= $this->e($user->address) ?></td>
        </tr>
        <tr>
            <td><?= $this->_('Birthday') ?></td>
            <td><?= $this->formatDate($user->birthday) ?></td>
        </tr>
    </table>
</fieldset>