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
     * @param \Phalcon\Config $config
     */
    public function __construct(\Phalcon\Config $config)
    {
        if (!isset($_GET['_url'])) {
            $this->fail();
        }

        $uri = explode('/', $_GET['_url']);

        if (!isset($uri[1])) {
            $this->fail();
        }

        if (isset($config->version) and $config->version) {
            $this->version = $uri[1];

            $this->controller = $uri[2] ?? 'index';

            $this->filename = APP_ROOT . $config->path . $this->version . '/' . $this->controller . '.php';
        } else {
            $this->version = false;

            $this->controller = $uri[1] ?? 'index';

            $this->filename = APP_ROOT . $config->path . $this->controller . '.php';
        }

        if (!is_file($this->filename)) {
            $this->fail();
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

    private function fail()
    {
        header("HTTP/1.1 404 Not Found");
        exit;
    }

}