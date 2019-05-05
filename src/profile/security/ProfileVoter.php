<?php
namespace App\profile\security;

use App\author\security\VoterAbstract;
use App\profile\entity\Profile;

class ProfileVoter extends VoterAbstract
{
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::LIST, self::READ, self::CREATE, self::UPDATE, self::DELETE])) {
            return false;
        }
        // only vote on Post objects inside this voter
        if (!$subject instanceof Profile) {
            return false;
        }

        return true;
    }

    protected function canList($subject, $user)
    {
        return true;
    }
}