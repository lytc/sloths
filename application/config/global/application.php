<?php

/* @var $this \Sloths\Application\Application */

error_reporting(E_ALL);

set_error_handler(function($errNo, $message, $file, $line) {
    throw new ErrorException($message, 0, $errNo, $file, $line);
});

set_exception_handler(function($exception) {
    try {

        if ($this->getRequest()->isXhr()) {
            $this->view->setLayout(false);
        }

        $body = $this->render(MODULE_SHARED_DIRECTORY . '/views/errors/error.html.php', [
            'exception' => $exception
        ]);

        $code = $exception->getCode();

        $this->getResponse()
            ->setStatusCode($code == 404 || $code == 403? $code : 500)
            ->setBody($body);

        return $this->send();
    } catch (\Exception $e) {
        echo $e->getMessage();
        echo '<br>';
        echo 'File: ' . $e->getFile();
        echo '<br>';
        echo 'Line: ' . $e->getLine();
        echo '<br>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    }

});

/**
 * Paths config
 */
$this->setPaths([

    'routes' => $this->getPath('routes'),
    'views' => $this->getPath('views'),
    'migrations' => realpath(__DIR__ . '/../../db/migrations')

]);


/**
 * Services
 */
$this->getServiceManager()->setServices([

    'view'              => 'Sloths\Application\Service\View',
    'translator'        => 'Sloths\Application\Service\Translator',
    'validator'         => 'Sloths\Application\Service\Validator',
    'redirector'        => 'Sloths\Application\Service\Redirector',
    'url'               => 'Sloths\Application\Service\Url',
    'session'           => 'Sloths\Application\Service\Session',
    'flash'             => 'Sloths\Application\Service\FlashSession',
    'message'           => 'Sloths\Application\Service\FlashMessage',
    'database'          => 'Sloths\Application\Service\Database',
    'auth'              => 'Sloths\Application\Service\Authenticator',
    'paginator'         => 'Sloths\Application\Service\Paginator',
    'dateTime'          => 'Sloths\Application\Service\DateTime',
    'mcrypt'            => 'Sloths\Application\Service\Mcrypt',
    'password'          => 'Sloths\Application\Service\Password',
    'cache'             => 'Sloths\Application\Service\CacheManager',

]);

/**
 * Dynamic methods
 */
$router = $this->getRouter();

$this->addDynamicMethods([
    'render' => function() {
        return call_user_func_array([$this->getServiceManager()->get('view'), 'render'], func_get_args());
    },
    'user' => function() {
        return $this->auth->getData();
    }

]);


/**
 * Dynamic properties
 */
$request = $this->getRequest();
$this->addDynamicProperties([
]);


/**
 * Event listeners
 */
$this->addEventListeners([

    /**
     * Before process matches route
     */
    'before' => function() {

    },

    /**
     * After send the response to client
     */
    'after' => function() {

    }
]);

\Sloths\Db\Model\AbstractModel::setDefaultConnectionManager($this->database);