<?php

namespace Pharest;


class Config extends \Phalcon\Config
{
    /** @var string $platform */
    public $platform;

    /** @var \DateTime $datetime */
    public $datetime;

    /** @var string $fresh */
    public $uri;

    /** @var string $method */
    public $method;

}