<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use LogicException;
use App\Entity\Character;

final class CharacterVoter extends Voter
{
    public const CHARACTER_DISPLAY = 'characterDisplay';

    public const CHARACTER_CREATE = 'characterCreate';

    private const ATTRIBUTES = [
        self::CHARACTER_CREATE,
        self::CHARACTER_DISPLAY,
    ];

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (null !== $subject) {
            return $subject instanceof Character && in_array($attribute, self::ATTRIBUTES);
        }
        return in_array($attribute, self::ATTRIBUTES);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {

        switch ($attribute) {
            case self::CHARACTER_CREATE:
                return $this->canCreate($token, $subject);
                break;

            case self::CHARACTER_DISPLAY:
                return $this->canDisplay($token, $subject);
                break;
        }

        throw new LogicException('Invalid attribute: ' . $attribute);
    }

    private function canDisplay($token, $subject)
    {
        return true;
    }

    private function canCreate($token, $subject){
        return true;
    }
}
