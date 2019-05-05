<?php
/**
 * Created by PhpStorm.
 * Profile: macos
 * Date: 2/15/19
 * Time: 2:28 PM
 */

namespace App\deputation\services;


interface AuthenServiceInterface
{
    public function loginCheck($information);
    public function loginSuccess();
    public function loginFail();

    public function logoutCheck();
    public function logoutSuccess();
    public function logoutFail();

    public function getMember();
    public function getHome();
}