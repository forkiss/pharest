<?php

namespace Pharest;

use Pharest\Exception\RouterException;

class Router
{
    /** @var string $router */
    protected $router;

    /** @var string $version */
    protected $version;

    /** @var string $controller */
    protected $controller;

    /** @var string $action */
    protected $action;

    /**
     * Router constructor.
     *
     * @param \Phalcon\Config $config
     */
    public function __construct(\Phalcon\Config $config)
    {
        if (!isset($_GET['_url'])) {
            throw new RouterException();
        }

        $uri = explode('/', $_GET['_url']);

        if (!isset($uri[1])) {
            throw new RouterException();
        }

        if (isset($config->version) and $config->version) {
            $this->version = $uri[1];

            $this->controller = !isset($uri[2]) ? 'index' : $uri[2];

            $this->action = !isset($uri[3]) ? 'index' : $uri[3];

            $this->router = APP_ROOT . $config->path . $this->version . '/' . $this->controller . '.php';
        } else {
            $this->version = false;

            $this->controller = !isset($uri[1]) ? 'index' : $uri[1];

            $this->action = !isset($uri[2]) ? 'index' : $uri[2];

            $this->router = APP_ROOT . $config->path . $this->controller . '.php';
        }

        if (!is_file($this->router)) {
            throw new RouterException($this->router);
        }
    }

    public function getRouter()
    {
        $router = new \Phalcon\Mvc\Micro\Collection();

        $router->setPrefix(($this->version ? '/' . $this->version : '') . '/' . $this->controller);

        require_once $this->router;

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
        return $this->controller;
    }

    /**
     * return current request counter action
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->action;
    }

}