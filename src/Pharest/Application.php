<?php

namespace Pharest;


class Application
{
    /** @var \Phalcon\Mvc\Micro $app */
    protected $app;

    /**
     * Handle constructor.
     */
    public function __construct()
    {
        $register = new \Pharest\Register\Register();

        $this->app = new \Phalcon\Mvc\Micro($register->injector());

        $this->middleware();

        $this->app->mount($this->app->finder->make());

        unset($register);

        $this->app->notFound(function () {
            $this->app->response->setStatusCode(404, 'Not Found')->sendHeaders();

            return $this->app->response;
        });

        $this->app->error(function ($exception) {
            $handler = new \App\Exception\Handler();

            $handler->handle($this->app->response, $exception);

            return $this->app->response;
        });
    }

    public function run()
    {
        $this->app->handle();
    }

    public function following(\Closure $closure)
    {
        $closure($this->app);
    }

    public function middleware()
    {
        if (!class_exists(\App\Middleware\Kernel::class)) {
            return false;
        }

        $kernel = new \App\Middleware\Kernel();

        /** @var \Pharest\Middleware\Immediately $middleware */
        foreach ($kernel->middleware as $key => $middleware) {
            $middleware = new $middleware();

            $middleware->call($this->app);
        }

        unset($kernel, $middleware);

        return true;
    }

}