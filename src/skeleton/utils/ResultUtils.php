<?php
namespace App\skeleton\utils;


use App\deputation\utils\ResultUtilsInterface;

class ResultUtils implements ResultUtilsInterface
{
    public function getMessage()
    {

    }

    public function setMessage($message)
    {

    }

    public function getSuggest()
    {

    }

    public function setSuggest($suggest)
    {

    }

    public function getObject()
    {
        return $this->object;
    }

    public function setObject($object)
    {
        $this->object = $object;
    }

    public function getCode()
    {

    }

    public function setCode($code)
    {

    }

    public function setSuccess()
    {
        $this->result = 1;
    }

    public function isSuccess()
    {
        return $this->result?1:0;
    }

    public function setFail()
    {
        $this->result = 0;
    }

    public function isFail()
    {
        return $this->result?0:1;
    }
}