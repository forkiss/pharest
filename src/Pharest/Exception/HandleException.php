<?php

namespace Pharest\Exception;


class HandleException
{
    /** @var bool $debug */
    protected $debug;


    public function __construct($debug = false)
    {
        $this->debug = $debug;
    }

    public function handle(\Phalcon\Http\Response &$response, \Exception &$exception)
    {
        if (class_exists(\App\Exception\Handler::class) and in_array(ExceptionHandler::class, class_implements(\App\Exception\Handler::class))) {

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

        return $response;
    }

    /**
     * Determine if the given exception is an Router exception.
     *
     * @param \Exception $e
     * @return bool
     */
    protected function isRouterException(\Exception $e)
    {
        return $e instanceof RouterException;
    }
}
