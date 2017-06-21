<?php

namespace Pharest;


class Model extends \Phalcon\Mvc\Model
{

    protected static $datetime;

    public static function setDatetime($datetime)
    {
        self::$datetime = $datetime;
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
                $message = self::getSchema() . ' data can not be found';
            }

            throw new \Pharest\Exception\ModelException($message, 100091);
        }

        return $query;
    }

    public function store($data = NULL)
    {
        if (parent::save($data) === false) {
            throw new \Pharest\Exception\ModelException(implode(',', $this->getMessages()), 100090);
        }
    }
}