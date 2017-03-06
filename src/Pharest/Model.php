<?php

namespace Pharest;


class Model extends \Phalcon\Mvc\Model
{
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
     * @param mixed  $parameters
     * @param string $message
     *
     * @return static
     */
    public static function firstOrFail($parameters, $message)
    {
        $query = parent::findFirst($parameters);

        if (!$query) {
            throw new \Pharest\Exception\NotFoundException();
        }

        return $query;
    }

}