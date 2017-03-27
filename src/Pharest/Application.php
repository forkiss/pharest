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

        $register = new \Pharest\Register\Register();

        $this->app = new \Phalcon\Mvc\Micro($register->injector($config));

        if (class_exists(\App\Middleware\Kernel::class)) {
            $register->middleware($this->app);
        }

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

}