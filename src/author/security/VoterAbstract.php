<?php

namespace App\author\security;

use App\authen\entity\Authen;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

//https://symfony.com/doc/current/security/voters.html
abstract class VoterAbstract implements VoterInterface
{
    const LIST   = 'list';
    const READ   = 'read';
    const CREATE = 'create';
    const UPDATE = 'update';
    const DELETE = 'delete';

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $subject, array $attributes)
    {
        // abstain vote by default in case none of the attributes are supported
        $vote = self::ACCESS_ABSTAIN;

        foreach ($attributes as $attribute) {
            if (!$this->supports($attribute, $subject)) {
                continue;
            }

            // as soon as at least one attribute is supported, default is to deny access
            $vote = self::ACCESS_DENIED;

            if ($this->voteOnAttribute($attribute, $subject, $token)) {
                // grant access as soon as at least one attribute returns a positive response
                return self::ACCESS_GRANTED;
            }
        }

        return $vote;
    }

    abstract protected function supports($attribute, $subject);

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof Authen) {
            // the user must be logged in; if not, deny access
            return false;
        }

        switch ($attribute) {
            case self::LIST:
                return $this->canList($subject, $user);
            case self::READ:
                return $this->canRead($subject, $user);
            case self::CREATE:
                return $this->canCreate($subject, $user);
            case self::UPDATE:
                return $this->canUpdate($subject, $user);
            case self::DELETE:
                return $this->canDelete($subject, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    protected function canList($subject, $user){
        return true;
    }

    protected function canRead($subject, $user){
        return true;
    }

    protected function canCreate($subject, $user){
        return true;
    }

    protected function canUpdate($subject, $user){
        return true;
    }

    protected function canDelete($subject, $user){
        return true;
    }

}