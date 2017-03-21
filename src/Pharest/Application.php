<?php

namespace Pharest;


class Application
{
    /** @var \Phalcon\Mvc\Micro $app */
    protected $app;

    /** @var \Phalcon\Di\FactoryDefault $di */
    protected $di;

    /** @var \Phalcon\Mvc\Micro\Collection $finder */
    protected $finder;

    /** @var  string $uri */
    protected $uri;

    /**
     * Handle constructor.
     */
    public function __construct()
    {
        $config = new Config(require_once APP_ROOT . '/app/config/config.php');

        $config->datetime = date('Y-m-d H:i:s');

        $this->registerDependency($config);

        $this->app = new \Phalcon\Mvc\Micro($this->di);

        $this->registerMiddleware();

        $this->app->mount((new Finder($config))->getCollection());

        $this->app->notFound(function () {
            $this->app->response->setStatusCode(404)->sendHeaders();

            return $this->app->response;
        });

        $this->app->error(function ($exception) {
            $handler = new ExceptionHandler();

            $handler->handle($this->app->response, $exception);

            return $this->app->response;
        });
    }

    public function run()
    {
        $this->app->handle();
    }

    private final function registerMiddleware()
    {
        if (class_exists(\App\Middleware\Kernel::class)) {
            $middleware = (new \App\Middleware\Kernel())->middleware;

            foreach ($middleware as $class => $action) {
                $this->app->{$action}(new $class());
            }

            return $middleware;
        }

        return [];
    }

    private final function registerDependency(\Pharest\Config &$config)
    {
        /**
         * The FactoryDefault Dependency Injector automatically registers the services that
         * provide a full stack framework. These default services can be overidden with custom ones.
         */
        $this->di = new \Phalcon\Di\FactoryDefault();

        /**
         * include dependencies
         */
        require_once APP_ROOT . '/app/config/dependencies.php';

        $config->method = $this->di->getShared('request')->getMethod();

        $config->uri = $this->di->getShared('request')->getURI();

        /**
         * Shared validator service
         */
        if (in_array($config->method, $config->app->validate->methods->toArray())) {
            $multi = $config->app->validate->multi;

            $this->di->setShared('validator', function () use(&$multi) {
                $validator = new \Pharest\Validate\Validator($multi);

                return $validator;
            });
        }

        /**
         * Shared configuration service
         */
        $this->di->setShared('config', function () use (&$config) {
            return $config;
        });
    }
}