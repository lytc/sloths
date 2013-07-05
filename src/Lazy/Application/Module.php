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
            $request->pathInfo('/');
            $application->request($request);
            return $application->run();
        }

        foreach ($this->modules as $requestBasePath => $callback) {
            if (preg_match('/^' . preg_quote($requestBasePath, '/') . '(.*)/', $requestPath, $matches)) {
                $application = $callback();

                $pathInfo = $matches[1]?: '/';
                if ('/' != $pathInfo[0]) {
                    $pathInfo = '/' . $pathInfo;
                }

                $request->pathInfo($pathInfo);
                $application->request($request);
                return $application->run();
            }
        }
    }
}