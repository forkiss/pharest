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

        $config->time = explode(' ', $config->datetime);

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

        if ($config->app->validate->methods->get($config->method, false)) {
            /**
             * Shared validator service
             */
            $di->setShared('validator', function () use (&$config) {
                $validator = new \Pharest\Validate\Validator($config);

                return $validator;
            });
        }

        $this->config = $config;

        unset($config);

        register_shutdown_function([$this, 'logger']);

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

            $prefix = '/' . $uri[1] . '/' . $controller;
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

    public function logger()
    {
        $_error = error_get_last();

        if (!empty($_error) and in_array($_error['type'], [1, 4, 16, 64, 256, 4096, E_ALL])) {
            $path = APP_ROOT . '/storage/logs/app';

            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $file = $path . '/error-log-' . $this->config->time[0] . '.txt';

            $message = $this->config->time[1] . ' - ' . json_encode([
                'type'    => $_error['type'],
                'message' => explode("\nStack trace:\n", $_error['message'])[0],
                'file'    => str_replace(APP_ROOT, '', $_error['file']),
                'line'    => $_error['line']
            ]) . "\n";

            file_put_contents($file, $message, FILE_APPEND);
        }

        exit;
    }

    private function fail($header)
    {
        header($header);
        exit;
    }

}