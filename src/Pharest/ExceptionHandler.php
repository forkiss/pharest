<?php

namespace Pharest;


class ExceptionHandler
{
    /** @var bool $debug */
    protected $debug;


    public function __construct($debug = false)
    {
        $this->debug = $debug;
    }

    public function handle(\Phalcon\Http\Response &$response, \Exception &$exception)
    {
        if ($this->hasCustomHandler()) {

            $handler = new \App\Exception\Handler();

            $handler->handle($response, $exception);

        } elseif ($this->debug) {

            $response->setStatusCode($exception->getCode());

            $response->setJsonContent([
                'message' => $exception->getMessage(),
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine()
            ]);

        } else {

            $response->setStatusCode($exception->getCode());

            $response->setJsonContent([
                'code'    => $exception->getCode(),
                'message' => $exception->getMessage()
            ]);

        }
    }

    /**
     * Determine if the app has custom exception handler
     *
     * @return bool
     */
    public function hasCustomHandler()
    {
        if (!class_exists(\App\Exception\Handler::class)) {
            return false;
        }

        if (!in_array(\Pharest\Exception\ExceptionHandler::class, class_implements(\App\Exception\Handler::class))) {
            return false;
        }

        return true;
    }
}