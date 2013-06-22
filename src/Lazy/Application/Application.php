<?php
namespace Lazy\Application;

use Lazy\Application\Exception\Exception;
use Lazy\Application\Exception\Halt;
use Lazy\Util\InstanceManager;
use Lazy\Session\Flash;
use Lazy\Http\Request;
use Lazy\Http\Response;
use Lazy\Util\String;
use Lazy\View\View;

class Application
{
    protected static $root;
    protected static $projectName;
    protected static $mounts = [];

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
    protected $baseRequestPath;
    protected $config = [];
    protected $session;
    protected $router = [];
    protected $request = [];
    protected $response = [];
    protected $view = [];
    protected $flash;

    protected $beforeCallbacks = [];

    public static function root($path = null)
    {
        if (!func_num_args()) {
            return self::$root;
        }

        self::$root = $path;
    }

    public static function project($name = null)
    {
        if (!func_num_args()) {
            return self::$projectName;
        }

        self::$projectName = $name;
    }

    public static function mount($requestPath, $appDir)
    {
        self::$mounts[$requestPath] = $appDir;
    }

    public static function run()
    {
        $request = new Request();
        $requestPath = $request->pathInfo();

        if (isset(self::$mounts[$requestPath])) {
            $baseRequestPath = $requestPath;
            $app = self::$mounts[$requestPath];
        } else {
            foreach (self::$mounts as $basePath => $appName) {
                $basePathEscaped = preg_quote($basePath, '/');
                if (preg_match('/^' . $basePathEscaped . '\//', $requestPath)) {
                    $baseRequestPath = $basePath;
                    $app = $appName;
                    break;
                }
            }
        }

        if (!isset($app)) {
            $baseRequestPath = '/';
            $app = self::$mounts['/'];
        }

        $appClassName =  String::camelize(true, $app);
        $appPath = self::$root . '/' . $app;

        require_once $appPath . '/' . $appClassName . '.php';
        $appClassName = self::project() . '\\' . $appClassName;
        $appInstance = new $appClassName($appPath, $baseRequestPath);
//        $appInstance->request($request);
        $appInstance->dispatch();
    }

    public function __construct($path, $baseRequestPath = null)
    {
        $parts = explode('\\', get_called_class());
        $this->id = end($parts);

        InstanceManager::register($this->id, $this);

        # convert error to error exception
        if (!self::$registeredErrorHandler) {
            set_error_handler(function($errNo, $errStr, $file, $line) {
                throw new \ErrorException($errStr, $errNo, 0, $file, $line);
            });
        }

        $this->path = realpath($path);
        $this->baseRequestPath = $baseRequestPath;

        # application env
        $env = getenv('APPLICATION_ENV');
        $env || $env = 'development';
        $this->env = $env;

        $configFiles = array();

        # global config
        $globalConfigFile = self::root() . '/configs/app.php';
        if (file_exists($globalConfigFile)) {
            $configFiles[] = $globalConfigFile;
        }

        $globalConfigEnvFile = self::root() . '/configs/' . $this->env . '.php';
        if (file_exists($globalConfigEnvFile)) {
            $configFiles[] = $globalConfigEnvFile;
        }

        # load config from app config
        $configPath = $this->path . '/configs/app.php';

        if (file_exists($configPath)) {
            $configFiles[] = $configPath;
        }

        # config by env
        $envConfigFile = $configPath . '/' . $this->env . '.php';
        if (file_exists($envConfigFile)) {
            $configFiles[] = $envConfigFile;
        }

        $config = array();
        foreach ($configFiles as $file) {
            $configPerFile = require_once $file;
            if (is_array($configPerFile)) {
                $config = array_replace_recursive($config, $configPerFile);
            }
        }

        $this->config($config);

        $includeFiles = array();

        # global include files
        $globalConfigIncludeDir = self::root() . '/configs/includes';
        if (file_exists($globalConfigIncludeDir)) {
            $dir = dir(($globalConfigIncludeDir));

            while (false !== ($f = $dir->read())) {
                if ('.' == $f || '..' == $f) {
                    continue;
                }

                $includeFiles[] = $globalConfigIncludeDir  . '/' . $f;
            }

            $dir->close();
        }

        # include
        $includeDir = $configPath . '/includes';
        if (file_exists($includeDir)) {
            $dir = dir($includeDir);
            while (false !== ($f = $dir->read())) {
                if ('.' == $f || '..' == $f) {
                    continue;
                }

                $includeFiles[] = $includeDir  . '/' . $f;
            }
            $dir->close();
        }

        foreach ($includeFiles as $file) {
            require_once $file;
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
        $this->dispatch();
        $this->halt();
    }

    public function dispatch()
    {
        try {
            $this->_before();

            $request = $this->request();
            $pathInfo = $request->pathInfo();

            if ($this->baseRequestPath && $this->baseRequestPath != '/') {
                $pathInfo = preg_replace('/^' . preg_quote($this->baseRequestPath, '/') . '/', '', $pathInfo);
                if (!$pathInfo) {
                    $pathInfo = '/';
                }
                $request->pathInfo($pathInfo);
                $pathInfo = $request->pathInfo();
            }

            $parts = explode('/', $pathInfo, 3);

            $controllerName = $parts[1];
            $controllerFile = $this->path . '/controllers/' . $controllerName  .'.php';
            if (!file_exists($controllerFile)) {
                $this->notFound();
            }

            require_once $controllerFile;

            foreach ($this->beforeCallbacks as $callback) {
                $callback = $callback->bindTo($this);
                $callback();
            }

            $routePath = isset($parts[2])? $parts[2] : '/';
            if ('/' != $routePath) {
                $routePath = '/' . trim($routePath, ' /');
            }

            ob_start();
            $result = $this->router()->dispatch($request->method(), $routePath);
            $buffer = ob_get_clean();

            if (false === $result) {
                $this->notFound();
            }

            $this->response()->body(is_object($result) || is_array($result)? $result : $buffer);
            $this->halt($this->response());
        } catch (Halt $e) {

        }
    }
}