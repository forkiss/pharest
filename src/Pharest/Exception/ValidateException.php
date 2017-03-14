<?php

namespace Pharest\Exception;


class ValidateException extends \RuntimeException
{

    /** @var \Phalcon\Validation\Message\Group $notice */
    protected $notice;

    public function setNotice(\Phalcon\Validation\Message\Group $notice)
    {
        $this->notice = $notice;
    }

    public function getNotice()
    {
        return $this->notice;
    }

    public function getMessages()
    {
        $message = [];

        foreach ($this->getNotice() as $item) {
            $message[] = [
                "field"   => $item->getField(),
                "message" => $item->getMessage()
            ];
        }

        return $message;
    }

}