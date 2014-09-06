<?php

$this->addEventListeners([
    'before' => function() {
        if (!$this->auth->exists()) {
            return $this->redirector->to('/auth?returnUrl=' . urlencode($this->url));
        }
    }
]);