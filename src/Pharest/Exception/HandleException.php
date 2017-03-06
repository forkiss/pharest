<?php

namespace Pharest\Exception;


class HandleException
{

    /** @var \Phalcon\Http\Response $response */
    protected $response;

    /** @var  \Exception $exception */
    protected $exception;

    /** @var bool $debug */
    protected $debug;


    public function __construct(&$response, \Exception &$exception, $debug = false)
    {
        $this->exception = $exception;
        $this->response = $response;
        $this->debug = $debug;
    }

    public function handle()
    {
        if (class_exists(\App\Exception\Handler::class)) {

            $handler = new \App\Exception\Handler();

            if (method_exists($handler, 'handle')) {
                return $handler->handle($this->response, $this->exception, $this->debug);
            }
        }

        if ($this->debug) {
            $this->response->setStatusCode($this->e->getCode());

            $this->response->setJsonContent([
                'message' => $this->e->getMessage(),
                'file'    => $this->e->getFile(),
                'line'    => $this->e->getLine()
            ]);
        } else {
            $this->response->setStatusCode($this->e->getCode());

            $this->response->setJsonContent([
                'code'    => $this->e->getCode(),
                'message' => $this->e->getMessage()
            ]);
        }

        return $this->response;
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
