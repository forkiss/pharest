<?php

namespace Pharest\Register;

class Router
{
    protected $router;

    protected $parser;

    protected $status;

    protected $prefix;

    protected $file;

    public function collection(\Pharest\Config &$config)
    {
        $this->status = $config->app->finder->fail_header;

        $this->parser($config->uri, APP_ROOT . $config->app->route->path, $config->app->route->version);

        $this->make();

        return $this->router;
    }

    private function parser($url, $path, $version)
    {
        $uri = explode('/', $url);

        if (!isset($uri[1]) or !$uri[1]) {
            $this->fail();
        }

        if ($version) {
            $controller = $uri[2] ?? 'index';

            $this->file = $path . $uri[1] . '/' . $controller . '.php';

            $this->prefix = '/' . $uri[1] . '/' . $controller;
        } else {
            $controller = $uri[1] ?? 'index';

            $this->file = $path . $controller . '.php';

            $this->prefix = '/' . $controller;
        }

        if (!is_file($this->file)) {
            $this->fail();
        }
    }

    private function make()
    {
        $router = new \Phalcon\Mvc\Micro\Collection();

        $router->setPrefix($this->prefix);

        require_once $this->file;

        $this->router = $router;

        unset($router);
    }

    private function fail()
    {
        header($this->status);
        exit;
    }

}