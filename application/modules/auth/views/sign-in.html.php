<br>
<br>

<div class="container">
    <div class="row">
        <div class="col-lg-6 col-lg-push-3 col-md-8 col-md-push-2 col-sm-10 col-sm-push-1 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title">Sign In</h2>
                </div>
                <div class="panel-body">
                    <form method="POST" action="<?= $this->url() ?>">
                        <input type="hidden" name="returnUrl" value="<?= $this->params()->returnUrl ?>">
                        <div class="form-group">
                            <input type="email" name="email" required autofocus placeholder="Email address" class="form-control">
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" required placeholder="Password" class="form-control">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                    </form>
                </div>
            </div>

            <? foreach ($this->message() as $message): ?>
                <? $map = ['error' => 'danger'] ?>
                <div class="alert alert-<?= isset($map[$message['type']])? $map[$message['type']] : $message['type'] ?>">
                    <?= $message['text'] ?>
                </div>
            <? endforeach ?>

        </div>
    </div>
</div>