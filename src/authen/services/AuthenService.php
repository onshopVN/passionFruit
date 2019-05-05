<?php

namespace App\authen\services;

use App\deputation\services\AuthenServiceInterface;

class AuthenService implements AuthenServiceInterface
{
    public function loginCheck($information)
    {
        // TODO: Implement login() method.
        $this->loginSuccess();
        return true;
    }
    public function loginSuccess()
    {
        // redirect to other plugin
    }
    public function loginFail()
    {
        // TODO: Implement loginFail() method.
    }


    public function logoutCheck()
    {
        // TODO: Implement logoutCheck() method.
    }
    public function logoutFail()
    {
        // TODO: Implement logoutFail() method.
    }
    public function logoutSuccess()
    {
        // TODO: Implement logoutSuccess() method.
    }


    public function getHome()
    {
        // TODO: Implement getHome() method.
    }
    public function getMember()
    {
        // TODO: Implement getMember() method.
    }

}