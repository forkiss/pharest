<?php

namespace Pharest\Register;

class Register
{
    /** @var \Pharest\Config $config */
    protected $config;

    public function injector()
    {
        $config = new \Pharest\Config(require_once APP_ROOT . '/app/config/config.php');

        ini_set('date.timezone', $config->app->timezone);

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

        return $di;
    }

    public function router()
    {
        $router = new Router();

        return $router->collection($this->config);
    }

}