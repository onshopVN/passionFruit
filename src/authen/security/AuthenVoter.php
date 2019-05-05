<?php


namespace App\authen\security;


use App\authen\entity\Authen;
use App\authen\repository\AuthenRepository;
use App\author\security\VoterAbstract;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

//https://symfony.com/doc/current/components/security/authorization.html
class AuthenVoter extends VoterAbstract
{

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::LIST, self::READ, self::CREATE, self::UPDATE, self::DELETE])) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Authen) {
            return false;
        }

        return true;
    }

    protected function canUpdate($object, $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user->getId() === $object->getId();
    }
}