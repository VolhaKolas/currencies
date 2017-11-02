<?php

namespace Core;

class Router
{
    private $routers = [
        'GET' => [],
        'POST' => []
    ];

    public function get($url, $action)
    {
        $this->routers['GET'][$url] = $action;
    }

    public function post($url, $action)
    {
        $this->routers['POST'][$url] = $action;
    }

    public function path($url)
    {
        if (array_key_exists($url, $this->routers[Request::getMethod()])) {
            list($class, $method) = explode('@', $this->routers[Request::getMethod()][$url]);
            return $this->callController('Controller\\' . $class, $method);
        }

        return $this->callController("Controller\\PageNotFoundController", "page");
    }

    public function run($file)
    {
        $router = new static;

        include "$file.php";

        return $router;
    }

    private function callController($class, $method)
    {
        $class = new $class;

        $class->$method();
    }
}