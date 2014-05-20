<?php $this->capture('title', 'My Site Title') ?>
<?php $this->capture('stylesheets', $this->stylesheetTag('/assets/stylesheets/application.css')) ?>
<?php $this->capture('javascripts', ['/assets/javascripts/foo.js', '/assets/javascripts/bar.js']) ?>
<p>Content</p>