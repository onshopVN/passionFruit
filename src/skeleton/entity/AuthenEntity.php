<?php
namespace App\skeleton\entity;


use App\deputation\entity\EntityInterface;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class  AuthenEntity implements EntityInterface,UserInterface
{

}