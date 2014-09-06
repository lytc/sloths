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
            <a class="navbar-brand" href="#">Sloths Account</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#"><span class="badge"><i class="fa fa-comments"></i> 12</span></a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle avatar-container" data-toggle="dropdown">
                        <img src="<?= $this->user()->avatar ?>">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-caret">
                        <li><a href="<?= $this->module('auth')->url->to('') ?>" data-method="DELETE"><i class="fa fa-power-off"></i> Sign Out</a></li>
                    </ul>
                </li>
            </ul>
            <ul class="navbar-right"></ul>
        </div>
    </div>
</div>

<div>
    <div class="sidebar">
        <a class="collapse-handle"><i class="fa fa-angle-double-left"></i></a>
        <ul class="nav nav-list">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="<?= $this->url('profile') ?>"><i class="fa fa-user"></i> Profile</a></li>
            <li><a href="#"><i class="fa fa-cogs"></i> Settings</a></li>
        </ul>
    </div>
    <div class="main-content">
        <?= $this->breadcrumb() ?>

        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <?= $this->content() ?>
                </div>
            </div>
        </div>


    </div>
</div>

