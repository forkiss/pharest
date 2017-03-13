<?php

namespace Pharest;


class Application
{
    /** @var \Phalcon\Mvc\Micro $app */
    protected $app;

    /** @var \Phalcon\Di\FactoryDefault $di */
    protected $di;

    /** @var  string $uri */
    protected $uri;

    /** @var  bool $debug */
    public $debug;

    /**
     * Handle constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $config = new Config(require_once $path);

        $this->debug = $config->application->debug ?? false;

        $finder = new Finder($config->application->route);

        $this->registerDependency($config, $finder);

        $this->app = new \Phalcon\Mvc\Micro($this->di);

        $this->registerMiddleware();

        $this->app->mount($finder->getCollection());

        $this->app->notFound(function () {
            $this->app->response->setStatusCode(404)->sendHeaders();

            return $this->app->response;
        });

        $this->app->error(function ($exception) {
            $handler = new ExceptionHandler($this->debug);

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

    private final function registerDependency(\Pharest\Config &$config, \Pharest\Finder &$finder)
    {
        /**
         * The FactoryDefault Dependency Injector automatically registers the services that
         * provide a full stack framework. These default services can be overidden with custom ones.
         */
        $this->di = new \Phalcon\Di\FactoryDefault();

        /**
         * include dependencies
         */
        require_once APP_ROOT . $config->application->dependencies->path;

        /**
         * Shared configuration service
         */
        $config->uri = $this->di->get('request')->getURI();

        $config->method = $this->di->get('request')->getMethod();

        $config->datetime = date('Y-m-d H:i:s');

        $this->di->setShared('config', function () use (&$config) {
            return $config;
        });

        $this->di->setShared('finder', function () use (&$finder) {
            return $finder;
        });

        if (in_array($config->method, ['POST', 'PUT'])) {
            $this->di->setShared('validator', function () {
                $validator = new \Pharest\Validate\Validator();

                return $validator;
            });
        }
    }
}