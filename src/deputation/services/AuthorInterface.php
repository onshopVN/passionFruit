<?php
/**
 * Created by PhpStorm.
 * Profile: macos
 * Date: 2/15/19
 * Time: 2:28 PM
 */

namespace App\deputation\services;


interface AuthorInterface
{
    public function getPermission($object);
    public function setPermission($object);
    public function checkExecute($object);
    public function checkRead($object);
    public function checkWrite($object);

}