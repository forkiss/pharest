<?php

namespace Pharest\Register;

class Finder
{

    public $uri;

    public $prefix;

    public $file;

    public $controller;

    public $version;

    /**
     * Router constructor.
     *
     * @param \Pharest\Config $config
     */
    public function __construct(\Pharest\Config &$config)
    {
        $this->uri = $config->uri;

        $this->parser($config->app->route->path);
    }

    public function make()
    {
        $router = new \Phalcon\Mvc\Micro\Collection();

        $router->setPrefix($this->prefix);

        require_once $this->file;

        return $router;
    }

    private function fail()
    {
        /** @var \Phalcon\Http\Response $response */
        $response = \Phalcon\Di::getDefault()->getShared('response');

        $response->setStatusCode(404);
        $response->send();

        exit();
    }

    private function parser($path)
    {
        if (strpos($this->uri, '?') !== false) {
            $explode = explode('?', $this->uri);
            $this->uri = $explode[0];
        }

        $uri = explode('/', $this->uri);

        if (!isset($uri[1]) or !$uri[1]) {
            $this->fail();
        }

        $this->version = $uri[1];

        $this->controller = $uri[2] ?? 'index';

        $this->prefix = '/' . $this->version . '/' . $this->controller;

        $this->file = APP_ROOT . $path . $this->prefix . '.php';

        if (!is_file($this->file)) {
            $this->fail();
        }

    }

}