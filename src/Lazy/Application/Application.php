<?php
namespace Lazy\Application;

use Lazy\Application\Exception\Exception;
use Lazy\Application\Exception\Halt;
use Lazy\Util\InstanceManager;
use Lazy\Session\Flash;
use Lazy\Http\Request;
use Lazy\Http\Response;
use Lazy\View\View;

class Application
{
    protected static $registeredErrorHandler = false;

    protected static $aliasMethods = [
        'session'       => 'session',
        'flash'         => 'flash',
        'paramsGet'     => 'request',
        'paramGet'      => 'request',
        'paramsPost'    => 'request',
        'paramPost'     => 'request',
        'paramsCookie'  => 'request',
        'paramCookie'   => 'request',
        'paramsFile'    => 'request',
        'paramFile'     => 'request',
        'params'        => 'request',
        'param'         => 'request',

        'status'        => 'response',
        'headers'       => 'response',
        'header'        => 'response',
        'contentType'   => 'response',
        'body'          => 'response',
        'send'          => 'response',

        'map'           => 'router',
        'get'           => 'router',
        'post'          => 'router',
        'put'           => 'router',
        'delete'        => 'router',
        'options'       => 'router',
        'trace'         => 'router',
        'path'          => 'router',

        'layout'        => 'view',
        'variables'     => 'view',
        'render'        => 'view',
        'display'       => 'view',

        'notFound'      => 'this'
    ];

    protected $id;
    protected $env;
    protected $path;
    protected $config = [];
    protected $session;
    protected $router = [];
    protected $request = [];
    protected $response = [];
    protected $view = [];
    protected $flash;

    protected $beforeCallbacks = [];

    public function __construct($path)
    {
        $this->id = end(explode('\\', get_called_class()));
        InstanceManager::register($this->id, $this);

        # convert error to error exception
        if (!self::$registeredErrorHandler) {
            set_error_handler(function($errNo, $errStr, $file, $line) {
                throw new \ErrorException($errStr, $errNo, 0, $file, $line);
            });
        }

        $this->path = realpath($path);

        # application env
        $env = getenv('APPLICATION_ENV');
        $env || $env = 'development';
        $this->env = $env;

        # load config from app config
        $configPath = $this->path . '/configs';

        # global config
        $config = require_once $configPath . '/app.php';

        # config by env
        $envConfigFile = $configPath . '/' . $this->env . '.php';
        if (file_exists($envConfigFile)) {
            $config = array_replace_recursive($config, require_once $envConfigFile);
        }

        $this->config($config);

        # include
        $includeDir = $configPath . '/includes';
        if (file_exists($includeDir)) {
            $dir = dir($includeDir);
            while (false !== ($f = $dir->read())) {
                if ('.' == $f || '..' == $f) {
                    continue;
                }

                require_once $includeDir . '/' . $f;
            }
            $dir->close();
        }

        $this->createAlias();
    }

    public function config($name = null, $value = null)
    {
        switch (func_num_args()) {
            case 0: return $this->config;

            case 1:
                if (is_array($name)) {
                    foreach ($name as $k => $v) {
                        $this->config($k, $v);
                    }
                    return $this;
                }
                return isset($this->config[$name])? $this->config[$name] : null;

            default:
                $this->config[$name] = $value;
                if (method_exists($this, $name)) {
                    $this->{$name}($value);
                }
                return $this;
        }
    }

    public function createAlias($namespace = null)
    {
        if (!$namespace) {
            $class = get_called_class();
            $namespace = substr($class, 0, strripos($class, '\\'));
        }

        foreach (self::$aliasMethods as $method => $class) {
            $code = "
                namespace {$namespace};
                function {$method}() {
                    return call_user_func_array([\\Lazy\\Util\\InstanceManager::{$this->id}(), '{$method}'], func_get_args());
                }";
            eval($code);
        }
    }

    public function __call($method, $args)
    {
        if (isset(self::$aliasMethods[$method])) {
            return call_user_func_array([$this->{self::$aliasMethods[$method]}(), $method], $args);
        }

        throw new Exception("Call undefined method `$method`");
    }

    public function session()
    {
        if (!$this->session) {
            InstanceManager::register('session', '\Lazy\Session\Session');
            $this->session = InstanceManager::session();
        }

        return call_user_func_array($this->session, func_get_args());
    }

    public function clean()
    {
        !ob_get_level() || ob_clean();
        return $this;
    }

    public function notFound()
    {
        $this->halt(404);
    }

    public function halt($status = null, $body = null)
    {
        $this->clean();
        if ($status instanceof Response) {
            $status->send();
        } else if (func_num_args()){
            if (is_string($status)) {
                $message = status;
                $status = null;
            }
            $this->response()->send($status, $body);
        }

        throw new Halt;
    }

    public function router($router = null)
    {
        if (!func_num_args()) {
            if (is_array($this->router)) {
                $this->router = new Router($this->router);
            }
            return $this->router;
        }

        $this->router = $router;

        return $this;
    }

    public function request($request = null)
    {
        if (!func_num_args()) {
            if (is_array($this->request)) {
                $this->request = new Request($this->request);
            }
            return $this->request;
        }

        $this->request = $request;

        return $this;
    }

    public function response($response = null)
    {
        if (!func_num_args()) {
            if (is_array($this->response)) {
                $this->response = new Response($this->response);
            }
            return $this->response;
        }

        $this->response = $response;

        return $this;
    }

    public function redirect()
    {
        $response = $this->response();
        call_user_func_array([$response, 'redirect'], func_get_args());
        $response->send();
        exit;
    }

    public function back()
    {
        return $this->redirect($this->request()->referrer());
    }

    public function view($view = null)
    {
        if (!func_num_args()) {
            if (is_array($this->view)) {
                $this->view = new View($this->view);
            }
            return $this->view;
        }

        $this->view = $view;

        return $this;
    }

    public function flash($name = null, $value = null)
    {
        if (!$this->flash) {
            $this->flash = new Flash();
        }

        if (!func_num_args()) {
            return $this->flash;
        }

        return call_user_func_array([$this->flash, 'data'], func_get_args());
    }

    protected function _before()
    {

    }

    public function before(\Closure $callback)
    {
        $this->beforeCallbacks[] = $callback;
        return $this;
    }

    public function call($method, $path)
    {
        $this->request()->method($method)->pathInfo($path);
        $this->run();
        $this->halt();
    }

    public function run()
    {
        try {
            $this->_before();

            foreach ($this->beforeCallbacks as $callback) {
                $callback = $callback->bindTo($this);
                $callback();
            }
            $request = $this->request();
            $pathInfo = $request->pathInfo();
            $parts = explode('/', $pathInfo, 3);

            $controllerName = $parts[1];
            $controllerFile = $this->path . '/controllers/' . $controllerName  .'.php';
            if (!file_exists($controllerFile)) {
                $this->notFound();
            }

            require_once $controllerFile;

            $routePath = isset($parts[2])? $parts[2] : '/';
            if ('/' != $routePath) {
                $routePath = '/' . trim($routePath, ' /');
            }

            ob_start();
            if (!$this->router()->dispatch($request->method(), $routePath)) {
                $this->notFound();
            }
            $this->response()->body(ob_get_clean());
            $this->halt($this->response());
        } catch (Halt $e) {

        }
    }
}