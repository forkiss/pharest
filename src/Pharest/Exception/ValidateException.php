<?php

namespace Pharest\Exception;


class ValidateException extends \RuntimeException
{

    /** @var \Phalcon\Validation\Message\Group $notice */
    protected $notice = [];

    public function setNotice(\Phalcon\Validation\Message\Group $notice)
    {
        $this->notice = $notice;
    }

    public function getNotice()
    {
        return $this->notice;
    }

    public function getFields()
    {
        $message = [];

        foreach ($this->getNotice() as $notice) {
            $message[] = [
                "field"   => $notice->getField(),
                "message" => $notice->getMessage()
            ];
        }

        return $message;
    }

}