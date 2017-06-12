<?php

namespace Pharest\Register;

class Router
{
    protected $parser;

    protected $status;

    protected $prefix;

    protected $file;

    public function collection(\Pharest\Config &$config)
    {
        $this->status = $config->app->finder->fail_header;

        $this->parser($config->uri, $config->app->route->path, $config->app->route->version);

        return $this->make();
    }

    private function make()
    {
        $router = new \Phalcon\Mvc\Micro\Collection();

        $router->setPrefix($this->prefix);

        require_once $this->file;

        return $router;
    }

    private function fail()
    {
        header($this->status);
        exit;
    }

    private function parser($url, $path, $version)
    {
        $uri = explode('/', $url);

        if (!isset($uri[1]) or !$uri[1]) {
            $this->fail();
        }

        if ($version) {
            if (isset($uri[2])) {
                if (strpos($uri[2], '?') !== false) {
                    $controller = explode('?', $uri[2])[0];
                } else {
                    $controller = $uri[2];
                }
            } else {
                $controller = 'index';
            }

            $this->file = APP_ROOT . $path . $uri[1] . '/' . $controller . '.php';

            $this->prefix = '/' . $uri[1] . '/' . $controller;
        } else {
            $controller = $uri[1] ?? 'index';

            $this->file = APP_ROOT . $path . $controller . '.php';

            $this->prefix = '/' . $controller;
        }

        if (!is_file($this->file)) {
            $this->fail();
        }
    }

}