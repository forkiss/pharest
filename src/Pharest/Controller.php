<?php

namespace Pharest;

/**
 * Class Controller
 *
 * @package \Pharest\Controller
 *
 * @property \Pharest\Validate\Validator validator
 * @property \Pharest\Register\Finder    finder
 * @property \Pharest\Config             config
 */
class Controller extends \Phalcon\Di\Injectable implements \Phalcon\Mvc\ControllerInterface
{
    /**
     * Phalcon\Mvc\Controller constructor
     */
    public final function __construct() { }

}