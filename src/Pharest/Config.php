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
            [
                'keys'   => [],
                'detail' => [
                    'message'      => [],
                    'cancelOnFail' => $this->app->validate->cancel_on_fail
                ]
            ],
            [
                'keys'   => [],
                'detail' => [
                    'message'      => [],
                    'domain'       => [],
                    'cancelOnFail' => $this->app->validate->cancel_on_fail
                ]
            ],
            [
                'keys'   => [],
                'detail' => [
                    'min'            => [],
                    'messageMinimum' => [],
                    'max'            => [],
                    'messageMaximum' => [],
                    'cancelOnFail'   => $this->app->validate->cancel_on_fail
                ]
            ]
        ];
    }

}