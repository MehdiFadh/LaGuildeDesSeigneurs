<?php

namespace App\Security\Voter;

use App\Entity\Building;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class BuildingVoter extends Voter
{
    public const BUILDING_DISPLAY = 'buildingDisplay';
    public const BUILDING_CREATE = 'buildingCreate';
    public const BUILDING_INDEX = 'buildingIndex';
    public const BUILDING_UPDATE = 'buildingUpdate';
    public const BUILDING_DELETE = 'buildingDelete';

    private const ATTRIBUTES = [
        self::BUILDING_UPDATE,
        self::BUILDING_CREATE,
        self::BUILDING_DISPLAY,
        self::BUILDING_INDEX,
        self::BUILDING_DELETE,
    ];

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (null !== $subject) {
            return $subject instanceof Building && in_array($attribute, self::ATTRIBUTES);
        }

        return in_array($attribute, self::ATTRIBUTES);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        switch ($attribute) {
            case self::BUILDING_CREATE:
                return $this->canCreate($token, $subject);
            case self::BUILDING_UPDATE:
                return $this->canUpdate($token, $subject);
            case self::BUILDING_DISPLAY:
            case self::BUILDING_INDEX:
                return $this->canDisplay($token, $subject);
            case self::BUILDING_DELETE:
                return $this->canDelete($token, $subject);
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
        return true;
    }

    private function canDelete($token, $subject)
    {
        return true;
    }
}
