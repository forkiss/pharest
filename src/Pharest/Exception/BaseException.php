<?php

namespace Pharest\Exception;


interface BaseException
{

    public function handle($request, \Exception $exception);

}