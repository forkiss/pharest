<?php

namespace Pharest;


class ExceptionHandler
{

    public function handle(\Phalcon\Http\Response &$response, \Exception &$exception)
    {
        if ($this->hasCustomHandler()) {

            $handler = new \App\Exception\Handler();

            $handler->handle($response, $exception);

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
