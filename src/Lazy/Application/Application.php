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
        'pickParams' => 'request',

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
    ];

    protected $path;
    protected $config = [];
    protected $session;
    protected $router;
    protected $request;
    protected $response;
    protected $view;
    protected $flash;
    protected $defaultRoute = 'index';

    protected $beforeCallbacks = [];

    public function __construct($path, array $config = [])
    {
        # convert error to error exception
        if (!self::$registeredErrorHandler) {
            set_error_handler(function($errNo, $errStr, $file, $line) {
                throw new \ErrorException($errStr, $errNo, 0, $file, $line);
            });
        }

        $this->path = realpath($path);
        $this->config($config);
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
                return $this;
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
                $body = $status;
                $status = null;
            }
            $this->response()->send($status, $body);
        }

        throw new Halt;
    }

    public function router($router = null)
    {
        if (!func_num_args()) {
            $this->router || $this->router = new Router($this->config('router')?: []);
            return $this->router;
        }

        $this->router = $router;

        return $this;
    }

    public function request($request = null)
    {
        if (!func_num_args()) {
            $this->request || $this->request = new Request($this->config('request')?: []);
            return $this->request;
        }

        $this->request = $request;

        return $this;
    }

    public function response($response = null)
    {
        if (!func_num_args()) {
            $this->response || $this->response = new Response($this->config('response')?: []);
            return $this->response;
        }

        $this->response = $response;

        return $this;
    }

    public function view($view = null)
    {
        if (!func_num_args()) {
            $this->view || $this->view = new View($this->config('view')?: []);
            return $this->view;
        }

        $this->view = $view;

        return $this;
    }

    public function flash($name = null, $value = null)
    {
        $this->flash || $this->flash = new Flash();

        if (!func_num_args()) {
            return $this->flash;
        }

        return call_user_func_array([$this->flash, 'data'], func_get_args());
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
            $request = $this->request();
            $pathInfo = $request->pathInfo();

            $parts = explode('/', $pathInfo);
            if (isset($parts[0]) && !$parts[0]) {
                array_shift($parts);
            }

            $routeParts = [];
            $routePath = '/';
            $routeFile = null;

            foreach ($parts as $index => $part) {
                $part = preg_replace_callback('/\W+/', function($matches) {
                    if (in_array($matches[0], ['-', '_'])) {
                        return $matches[0];
                    }
                    return '';
                }, $part);

                $routeParts[] = $part;
                $file = $this->path . '/routes/' . implode('/', $routeParts) . '.php';
                if (file_exists($file)) {
                    $routeFile = $file;
                    $routePath = '/' . implode('/', array_slice($parts, $index + 1));
                }

                $file = $this->path . '/routes/' . implode('/', $routeParts) . '/index.php';
                if (file_exists($file)) {
                    $routeFile = $file;
                    $routePath = '/' . implode('/', array_slice($parts, $index));
                }
            }

            if (!$routeFile) {
                $routeFile = $this->path . '/routes/' . $this->defaultRoute . '.php';
                $routePath = $pathInfo;
            }

            require_once $routeFile;

            foreach ($this->beforeCallbacks as $callback) {
                $callback = $callback->bindTo($this);
                $callback();
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