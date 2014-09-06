<?php

/* @var $this \Sloths\Application\Service\View */

$this

    ->assets([
        'jquery' => '//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js',
        'bootstrap' => [
            'extends' => 'jquery',
            'sources' => [
                '//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css',
                '//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css',
                '//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js'
            ]
        ],

        'html-editor' => [
            'extends' => 'jquery',
            'sources' => [
                '//cdnjs.cloudflare.com/ajax/libs/tinymce/4.1.2/tinymce.jquery.min.js',
                '//cdnjs.cloudflare.com/ajax/libs/tinymce/4.1.2/jquery.tinymce.min.js',
                '/assets/_shared/javascripts/html-editor.js'
            ]
        ],

        'common' => [
            'extends' => 'bootstrap',
            'sources' => [
                '/assets/_shared/javascripts/framework.js'
            ]
        ]
    ])

//    ->setVersionParamsName('___') // default to "___"
    ->setVersion($this->getApplication()->getEnv() == 'development'? time() : 'v1')
    ->uses('application')
;

$this->setHelpers([
    /**
     * app helper
     * $this->app() // -> Sloths\Application\Application
     */
    'app' => function() {
        return $this->getApplication();
    },

    /**
     * message helper
     * $this->message() // -> Sloths\Application\Service\FlashMessage
     */
    'message' => function() {
        return $this->getApplication()->message;
    },

    /**
     * url helper
     * $this->url() // -> Sloths\Application\Service\Url
     */
    'url' => function() {
        return call_user_func_array($this->getApplication()->url, func_get_args());
    },

    /**
     * module helper
     * $this->module('admin')
     */
    'module' => function($moduleName) {
        return $this->getApplication()->getModuleManager()->get($moduleName);
    },

    /**
     * auth helper
     * $this->auth() // -> Sloths\Application\Service\Authenticator
     */
    'auth' => function() {
        return $this->getApplication()->auth;
    },

    /**
     * user service
     * $this->user() // -> Application\Model\User
     */
    'user' => function() {
        return $this->getApplication()->auth->getData();
    },

    /**
     * params helper
     * $this->params() // -> Sloths\Misc\Parameters
     */
    'params' => function($name = null) {
        $params = $this->getApplication()->getRequest()->getParams();

        if ($name) {
            return $params->get($name);
        }
        return $params;
    },

    /**
     * shorthand for escape
     */
    'e' => function($str) {
        return $this->escape($str);
    },

    /**
     * breadcrumb helper
     * $this->breadcrumb() // -> string
     * $this->breadcrumb(["title" => "/url"])
     */
    'breadcrumb' => function(array $items = null) {
        static $breadcrumbItems = [];

        if (null === $items) {
            return $this->partial(MODULE_SHARED_DIRECTORY . '/views/breadcrumb', ['items' => $breadcrumbItems]);
        } else {
            $breadcrumbItems = array_merge($breadcrumbItems, $items);
        }
    },

    'paginate' => function(\Sloths\Pagination\Paginator $paginator, $template = 'sliding') {
            $template = MODULE_SHARED_DIRECTORY . '/views/pagination-controls/' . $template . '.html.php';
            return $this->partial($template, ['paginator' => $paginator]);
        },

    '_' => function($text = null) {
            $translator = $this->getApplication()->translator;

            if ($text) {
                return $translator->translate($text);
            }

            return $translator;
        },

    'formatDate' => function() {
            return call_user_func_array([$this->getApplication()->dateTime, 'formatDate'], func_get_args());
        },

    'formatDateTime' => function() {
    return call_user_func_array([$this->getApplication()->dateTime, 'formatDateTime'], func_get_args());
}
]);

