<?php

namespace Pharest;


class Application
{
    /** @var \Phalcon\Mvc\Micro $app */
    protected $app;

    /** @var \Phalcon\Di\FactoryDefault $di */
    protected $di;

    /** @var \Phalcon\Mvc\Micro\Collection $router */
    protected $router;

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

        $this->router = new Router($config->application->route);

        $this->registerDependency($config);

        $this->app = new \Phalcon\Mvc\Micro($this->di);

        $this->registerMiddleware();

        $this->app->mount($this->router->getRouter());

        $this->app->notFound(function () {
            $this->app->response->setStatusCode(404)->sendHeaders();
        });

        $this->app->error(function ($exception) {
            $handler = new \Pharest\Exception\HandleException($this->debug);

            return $handler->handle($this->app->response, $exception);
        });
    }

    public function run()
    {
        $this->app->handle();
    }

    final private function registerMiddleware()
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

    final private function registerDependency(\Pharest\Config &$config)
    {
        /**
         * The FactoryDefault Dependency Injector automatically registers the services that
         * provide a full stack framework. These default services can be overidden with custom ones.
         */
        $this->di = new \Phalcon\Di\FactoryDefault();

        if (is_file(APP_ROOT . $config->application->dependencies->path)) {
            /**
             * include services
             */
            require_once APP_ROOT . $config->application->dependencies->path;
        } else {
            /**
             * Shared configuration service
             */
            $this->di->setShared('config', function () use (&$config) {
                return $config;
            });

            /**
             * Database connection is created based in the parameters defined in the configuration file
             */
            $this->di->setShared('db', function () use (&$config) {
                $adapter = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;

                return new $adapter([
                    'host'     => $config->database->host,
                    'username' => $config->database->username,
                    'password' => $config->database->password,
                    'dbname'   => $config->database->dbname,
                    'charset'  => $config->database->charset
                ]);
            });
        }
    }
}