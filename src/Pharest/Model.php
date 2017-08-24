<?php

namespace Pharest;


class Model extends \Phalcon\Mvc\Model
{

    protected static $datetime;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        \Phalcon\Mvc\Model::setup(['notNullValidations' => false]);
        $this->setSchema("ebao");
    }

    public static function setDatetime($datetime)
    {
        self::$datetime = $datetime;
    }

    public static function getDatetime()
    {
        return self::$datetime;
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return static[]|static
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed  $parameters
     *
     * @return static
     */
    public static function first($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed  $parameters
     * @param string $message
     *
     * @return static
     */
    public static function firstOrFail($parameters, $message = '')
    {
        $query = parent::findFirst($parameters);

        if (!$query) {

            if (!$message) {
                $message = 'data can not be found';
            }

            throw new \Pharest\Exception\ModelException($message, 100091);
        }

        return $query;
    }

    public function store($data = null)
    {
        if (parent::save($data) === false) {
            throw new \Pharest\Exception\ModelException(implode(',', $this->getMessages()), 100090);
        }
    }

    public function create($data = null, $whiteList = null)
    {
        if (parent::create($data, $whiteList) === false) {
            throw new \Pharest\Exception\ModelException(implode(',', $this->getMessages()), 100090);
        }
    }

    public function update($data = null, $whiteList = null)
    {
        if (parent::update($data, $whiteList) === false) {
            throw new \Pharest\Exception\ModelException(implode(',', $this->getMessages()), 100090);
        }
    }

}