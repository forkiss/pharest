<?php

namespace Pharest;


class Config extends \Phalcon\Config
{
    /** @var string $platform */
    public $platform;

    /** @var \DateTime $datetime */
    public $datetime;

    /** @var string $method */
    public $method;

    /** @var string $uri */
    public $uri;

    /** @var  string $client_ip */
    public $client_ip;

    /** @var \App\Models\User|\App\Models\Users $user */
    public $user;

    /** @var string */
    public $token;

}