<?php

namespace Pharest\Register;

class Finder
{

    public $uri;

    public $prefix;

    public $file;

    public $controller;

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

        $this->controller = $uri[2] ?? 'index';

        $this->file = APP_ROOT . $path . $uri[1] . '/' . $this->controller . '.php';

        $this->prefix = '/' . $uri[1] . '/' . $this->controller;

        if (!is_file($this->file)) {
            $this->fail();
        }
    }

}