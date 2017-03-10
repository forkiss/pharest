<?php

namespace Pharest;

/**
 * Class Controller
 *
 * @package \Pharest\Controller
 *
 * @property \Pharest\Config config
 * @property \Pharest\Finder finder
 */
abstract class Controller extends \Phalcon\Di\Injectable implements \Phalcon\Mvc\ControllerInterface
{
    /**
     * Phalcon\Mvc\Controller constructor
     */
    public final function __construct() { }

}