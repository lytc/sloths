<br>
<br>

<div class="container">
    <div class="row">
        <div class="col-lg-6 col-lg-push-3 col-md-8 col-md-push-2 col-sm-10 col-sm-push-1 col-xs-12">
            <div class="well text-center">
                <h2 class="text-primary">Hi <?= $this->e($this->user()->name) ?>!</h2>
                <form action="<?= $this->url('') ?>" method="POST">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-primary">Sign Out</button>
                </form>
            </div>
        </div>
    </div>
</div>