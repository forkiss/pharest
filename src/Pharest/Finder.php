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

    /** @var string $action */
    protected $action;

    /**
     * Finder constructor.
     *
     * @param \Phalcon\Config $config
     */
    public function __construct(\Phalcon\Config $config)
    {
        if (!isset($_GET['_url'])) {
            throw new \Pharest\Exception\FinderException();
        }

        $uri = explode('/', $_GET['_url']);

        if (!isset($uri[1])) {
            throw new \Pharest\Exception\FinderException();
        }

        if (isset($config->version) and $config->version) {
            $this->version = $uri[1];

            $this->controller = $uri[2] ?? 'index';

            $this->action = $uri[3] ?? 'index';

            $this->filename = APP_ROOT . $config->path . $this->version . '/' . $this->controller . '.php';
        } else {
            $this->version = false;

            $this->controller = $uri[1] ?? 'index';

            $this->action = $uri[2] ?? 'index';

            $this->filename = APP_ROOT . $config->path . $this->controller . '.php';
        }

        if (!is_file($this->filename)) {
            throw new \Pharest\Exception\FinderException();
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