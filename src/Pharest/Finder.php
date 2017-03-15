<?php

namespace Pharest;


class Finder
{
    /** @var string $filename */
    protected $filename;

    /** @var string $version */
    protected $version;

    /** @var string $controller */
    protected $controller;

    /**
     * Finder constructor.
     *
     * @param \Pharest\Config $config
     */
    public function __construct(\Pharest\Config &$config)
    {
        if (!isset($config->uri)) {
            $this->fail($config->app->finder->fail_header);
        }

        $uri = explode('/', $config->uri);

        if (isset($config->app->route->version) and $config->app->route->version) {
            $this->version = $uri[1];

            $this->controller = $uri[2] ?? 'index';

            $this->filename = $config->app->route->path . $this->version . '/' . $this->controller . '.php';
        } else {
            $this->version = false;

            $this->controller = $uri[1] ?? 'index';

            $this->filename = $config->app->route->path . $this->controller . '.php';
        }

        if (!is_file($this->filename)) {
            $this->fail($config->app->finder->fail_header);
        }
    }

    public function getCollection()
    {
        $router = new \Phalcon\Mvc\Micro\Collection();

        $router->setPrefix(($this->version ? '/' . $this->version : '') . '/' . $this->controller);

        require_once $this->filename;

        return $router;
    }

    /**
     * return current api version
     *
     * @return string
     */
    public function getRouterVersion()
    {
        return $this->version;
    }

    /**
     * return current request counter controller
     *
     * @return string
     */
    public function getControllerName()
    {
        return ucfirst($this->controller);
    }

    private function fail($header)
    {
        header($header);
        exit;
    }

}