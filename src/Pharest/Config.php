<?php

namespace Pharest;

class Config extends \Phalcon\Config
{
    /** @var string $platform */
    public $platform;

    /** @var \DateTime $datetime */
    public $datetime;

    /** @var array $request */
    public $request = [];

    public function setRequest($method, $uri)
    {
        $this->request['method'] = $method;

        $this->request['url'] = $uri;
    }

}