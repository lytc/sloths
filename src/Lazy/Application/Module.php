<?php

namespace Lazy\Application;

use Lazy\Http\Request;

class Module
{
    protected $modules = [];

    public function add($requestBasePath, $callback = null)
    {
        if (is_array($requestBasePath)) {
            $this->modules = array_merge($this->modules, $requestBasePath);
        } else {
            $this->modules[$requestBasePath] = $callback;
        }
        return $this;
    }

    public function run()
    {
        $request = new Request();
        $requestPath = $request->pathInfo();
        if (isset($this->modules[$requestPath])) {
            $application = $this->modules[$requestPath]();
            $application->request()->basePath(rtrim($requestPath, '/'));
            return $application->run();
        }

        uksort($this->modules, function($a, $b) {
            return strlen($a) < strlen($b);
        });

        foreach ($this->modules as $requestBasePath => $callback) {
            if ($requestBasePath != '/' && !substr($requestBasePath, -1) != '/') {
                $requestBasePath = $requestBasePath . '/';
            }

            if (preg_match('/^' . preg_quote($requestBasePath, '/') . '(.*)/', $requestPath, $matches)) {
                $application = $callback();

                $application->request()->basePath(rtrim($requestBasePath, '/'));
                return $application->run();
            }
        }
    }
}