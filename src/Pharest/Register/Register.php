<?php

namespace Pharest\Register;

class Register
{

    public function injector(\Pharest\Config &$config)
    {
        /**
         * The FactoryDefault Dependency Injector automatically registers the services that
         * provide a full stack framework. These default services can be overidden with custom ones.
         */
        $di = new \Phalcon\Di\FactoryDefault();

        $config->setRequest($di->getShared('request')->getMethod(), $di->getShared('request')->getURI());

        /**
         * include dependencies
         */
        require_once APP_ROOT . $config->app->dependencies->path;

        /**
         * Shared validator service
         */
        if ($config->app->validate->methods->get($config->request['method'])) {
            $multi = $config->app->validate->multi;

            $di->setShared('validator', function () use(&$multi) {
                $validator = new \Pharest\Validate\Validator($multi);

                return $validator;
            });
        }

        /**
         * Shared configuration service
         */
        $di->setShared('config', function () use (&$config) {
            return $config;
        });

        return $di;
    }

    public function middleware(\Phalcon\Mvc\Micro &$app)
    {
        $kernel = new \App\Middleware\Kernel();

        /** @var \Pharest\Middleware\Immediately $middleware */
        foreach ($kernel->middleware as $middleware) {
            $middleware = new $middleware();

            $middleware->call($app);
        }

        return true;
    }

}