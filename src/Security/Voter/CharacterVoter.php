<?php

// src/Security/Voter/CharacterVoter.php
// Voter de sécurité chargé de vérifier les autorisations de lecture, création, modification et suppression des personnages (seul le propriétaire du personnage ou un administrateur peut modifier/supprimer).

namespace App\Security\Voter;

use App\Entity\Character;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class CharacterVoter extends Voter
{
    public const CHARACTER_DISPLAY = 'characterDisplay';

    public const CHARACTER_CREATE = 'characterCreate';

    public const CHARACTER_INDEX = 'characterIndex';

    public const CHARACTER_UPDATE = 'characterUpdate';

    public const CHARACTER_DELETE = 'characterDelete';

    private const ATTRIBUTES = [
        self::CHARACTER_UPDATE,
        self::CHARACTER_CREATE,
        self::CHARACTER_DISPLAY,
        self::CHARACTER_INDEX,
        self::CHARACTER_DELETE,
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

            case self::CHARACTER_UPDATE:
                return $this->canUpdate($token, $subject);
                break;

            case self::CHARACTER_DISPLAY:
            case self::CHARACTER_INDEX:
                return $this->canDisplay($token, $subject);
                break;

            case self::CHARACTER_DELETE:
                return $this->canDelete($token, $subject);
                break;
        }

        throw new \LogicException('Invalid attribute: '.$attribute);
    }

    private function canDisplay($token, $subject)
    {
        return true;
    }

    private function canCreate($token, $subject)
    {
        return true;
    }

    private function canUpdate($token, $subject)
    {
        return $this->accessDecisionManager->decide($token, ['ROLE_ADMIN']) || $subject->getUser() === $token->getUser();
    }

    private function canDelete($token, $subject)
    {
        return $this->accessDecisionManager->decide($token, ['ROLE_ADMIN']) || $subject->getUser() === $token->getUser();
    }

    public function __construct(
        private AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }
}
