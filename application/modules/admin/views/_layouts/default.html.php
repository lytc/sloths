<? $this->setLayout(MODULE_SHARED_DIRECTORY . '/views/_layouts/common') ?>

<div class="top-navbar navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Sloths Admin</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#"><span class="badge"><i class="fa fa-comments"></i> 12</span></a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle avatar-container" data-toggle="dropdown">
                        <img src="<?= $this->user()->avatar ?>">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-caret">
                        <li>
                            <a href="<?= $this->module('account')->url->to('') ?>">
                                <div class="text-info">
                                    <?= $this->e($this->user()->name) ?>
                                    <div class="text-muted">
                                        <small><?= $this->e($this->user()->email) ?></small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="<?= $this->module('auth')->url->to('') ?>" data-method="DELETE"><i class="fa fa-power-off"></i> <?= $this->_('Sign Out') ?></a></li>
                    </ul>
                </li>
            </ul>
            <ul class="navbar-right"></ul>
        </div>
    </div>
</div>

<div>
    <div class="sidebar">
        <ul class="nav nav-list">
            <li><a href="#"><i class="fa fa-dashboard"></i> <?= $this->_('Dashboard') ?></a></li>
            <li>
                <a href="#am-list" data-toggle="collapse"><i class="fa fa-users"></i> <?= $this->_('Account management') ?> <i class="arrow fa fa-angle-down"></i></a>
                <ul id="am-list" class="nav nav-list collapse">
                    <li><a href="<?= $this->url('users') ?>"><i class="fa fa-caret-right"></i> <?= $this->_('Users') ?></a></li>
                    <li><a href="<?= $this->url('roles') ?>"><i class="fa fa-caret-right"></i> <?= $this->_('Roles') ?></a></li>
                    <li><a href="<?= $this->url('permissions') ?>"><i class="fa fa-caret-right"></i> <?= $this->_('Permissions') ?></a></li>
                </ul>
            </li>
            <li>
                <a href="#cm-list" data-toggle="collapse"><i class="fa fa-edit"></i> <?= $this->_('Content management') ?> <i class="arrow fa fa-angle-down"></i></a>
                <ul id="cm-list" class="nav nav-list collapse">
                    <li><a href="<?= $this->url('posts') ?>"><i class="fa fa-caret-right"></i> <?= $this->_('Posts') ?></a></li>
                    <li><a href="<?= $this->url('faqs') ?>"><i class="fa fa-caret-right"></i> <?= $this->_('FAQs') ?></a></li>
                </ul>
            </li>
            <li>
                <a href="#s-list" data-toggle="collapse"><i class="fa fa-cogs"></i> <?= $this->_('Settings') ?>  <i class="arrow fa fa-angle-down"></i></a>
                <ul id="s-list" class="nav nav-list collapse">
                    <li><a href="<?= $this->url('smtp') ?>"><i class="fa fa-caret-right"></i> SMTP</a></li>
                </ul>
            </li>
        </ul>
    </div>
    <div class="main-content">

        <?= $this->breadcrumb() ?>

        <div class="container-fluid">
            <div id="global-alert" class="alert alert-danger" style="display: none"></div>
            <div class="row">
                <div class="col-xs-12">
                    <?= $this->content() ?>
                </div>
            </div>
        </div>


    </div>
</div>

