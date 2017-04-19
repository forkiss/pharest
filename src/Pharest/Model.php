<?php

namespace Pharest;


abstract class Model extends \Phalcon\Mvc\Model
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

            throw new \Pharest\Exception\NotFoundException($message);
        }

        return $query;
    }

    public function save($data = null, $whiteList = null)
    {
        if (parent::save($data, $whiteList) === false) {
            throw new \Pharest\Exception\ModelException('server busy', 100090);
        }
    }

}