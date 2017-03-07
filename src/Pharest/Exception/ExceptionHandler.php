<?php

namespace Pharest\Exception;


interface ExceptionHandler
{

    public function handle(\Phalcon\Http\Response &$response, \Exception $exception);

}