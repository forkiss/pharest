<?php

namespace Pharest\Register;

class Register
{
    /** @var \Pharest\Config $config */
    protected $config;

    public function injector()
    {

        $config = new \Pharest\Config(require_once APP_ROOT . '/app/config/config.php');

        /**
         * The FactoryDefault Dependency Injector automatically registers the services that
         * provide a full stack framework. These default services can be overidden with custom ones.
         */
        $di = new \Phalcon\Di\FactoryDefault();

        $config->datetime = date('Y-m-d H:i:s');

        $config->method = $di->getShared('request')->getMethod();

        $config->uri = $di->getShared('request')->getURI();

        /**
         * Shared configuration service
         */
        $di->setShared('config', function () use (&$config) {
            return $config;
        });

        /**
         * include dependencies
         */
        require_once APP_ROOT . $config->app->dependencies->path;

        if ($config->app->validate->methods->get($config->method)) {
            /**
             * Shared validator service
             */
            $multi = $config->app->validate->multi;

            $di->setShared('validator', function () use (&$multi) {
                $validator = new \Pharest\Validate\Validator($multi);

                return $validator;
            });
        }

        $this->config = $config;

        unset($config);

        return $di;
    }

    public function middleware(\Phalcon\Mvc\Micro &$app)
    {
        if (!class_exists(\App\Middleware\Kernel::class)) {
            return false;
        }

        $kernel = new \App\Middleware\Kernel();

        /** @var \Pharest\Middleware\Immediately $middleware */
        foreach ($kernel->middleware as $middleware) {
            $middleware = new $middleware();

            $middleware->call($app);
        }

        unset($kernel, $middleware);

        return true;
    }

    public function router()
    {
        $uri = explode('/', $this->config->uri);

        if (!isset($uri[1]) or !$uri[1]) {
            $this->fail($this->config->app->finder->fail_header);
        }

        if ($this->config->app->route->version) {
            $controller = $uri[2] ?? 'index';

            $file = APP_ROOT . $this->config->app->route->path . $uri[1] . '/' . $controller . '.php';

            $prefix  = '/' . $uri[1] . '/' . $controller;
        } else {
            $controller = $uri[1] ?? 'index';

            $file = APP_ROOT . $this->config->app->route->path . $controller . '.php';

            $prefix = '/' . $controller;
        }

        if (!is_file($file)) {
            $this->fail($this->config->app->finder->fail_header);
        }

        $router = new \Phalcon\Mvc\Micro\Collection();

        $router->setPrefix($prefix);

        require_once $file;

        unset($uri, $controller, $file, $prefix);

        return $router;
    }

    private function fail($header)
    {
        header($header);
        exit;
    }
}