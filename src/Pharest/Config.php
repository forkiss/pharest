<?php

namespace Pharest;


class Config extends \Phalcon\Config
{
    /** @var string $platform */
    public $platform;

    /** @var array $time */
    public $time;

    /** @var \DateTime $datetime */
    public $datetime;

    /** @var string $request */
    public $method;

    /** @var string $request */
    public $uri;

}