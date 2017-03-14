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

    public function initValidatorRulers()
    {
        return [
            ['keys' => [], 'detail' => ['message' => []]],
            ['keys' => [], 'detail' => ['message' => [], 'domain' => []]],
            ['keys' => [], 'detail' => ['min' => [], 'messageMinimum' => [], 'max' => [], 'messageMaximum' => []]]
        ];
    }
}