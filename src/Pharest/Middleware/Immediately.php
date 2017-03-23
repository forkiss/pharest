<?php

namespace Pharest\Middleware;

interface Immediately
{

    public function call(\Phalcon\Mvc\Micro &$app);

}