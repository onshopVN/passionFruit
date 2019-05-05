<?php
/**
 * Created by PhpStorm.
 * Profile: macos
 * Date: 2/15/19
 * Time: 2:28 PM
 */

namespace App\deputation\utils;


interface ResultUtilsInterface
{
    public function getMessage();
    public function setMessage($message);

    public function getSuggest();
    public function setSuggest($suggest);

    public function getObject();
    public function setObject($object);

    public function getCode();
    public function setCode($code);

    public function setSuccess();
    public function isSuccess();

    public function setFail();
    public function isFail();


}