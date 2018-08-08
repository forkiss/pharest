<?php

namespace Pharest;


class Config extends \Phalcon\Config
{
    /** @var string $platform */
    public $platform;

    /** @var string $version */
    public $version;

    /** @var string $channel */
    public $channel;

    /** @var \DateTime $datetime */
    public $datetime;

    /** @var string $method */
    public $method;

    /** @var string $uri */
    public $uri;

    /** @var  string $client_ip */
    public $client_ip;

    /** @var \App\Models\User $user */
    public $user;

    /** @var string $token */
    public $token;

    /** @var string $category */
    public $category;

    /** @var string $request_id */
    public $request_id;

}