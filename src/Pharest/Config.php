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
                    'cancelOnFail' => !$this->app->validate->multi
                ]
            ],
            [
                'keys'   => [],
                'detail' => [
                    'message'      => [],
                    'domain'       => [],
                    'cancelOnFail' => !$this->app->validate->multi
                ]
            ],
            [
                'keys'   => [],
                'detail' => [
                    'min'            => [],
                    'messageMinimum' => [],
                    'max'            => [],
                    'messageMaximum' => [],
                    'cancelOnFail'   => !$this->app->validate->multi
                ]
            ],
            [
                'keys'   => [],
                'detail' => [
                    'minimum'      => [],
                    'maximum'      => [],
                    'message'      => [],
                    'cancelOnFail' => !$this->app->validate->multi
                ]
            ]
        ];
    }

}