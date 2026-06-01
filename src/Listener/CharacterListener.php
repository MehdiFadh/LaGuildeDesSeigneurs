<?php

// src/Listener/CharacterListener.php
// Écouteur d'événements (EventSubscriber) qui intercepte la création et la mise à jour des personnages pour appliquer des ajustements dynamiques sur leurs statistiques (intelligence, force selon leur type).

namespace App\Listener;

use App\Event\CharacterEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CharacterListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        // Événements que l'on souhaite écouter
        return [
            CharacterEvent::CHARACTER_CREATED => 'characterCreated', // Nom de la méthode appelée
            CharacterEvent::CHARACTER_UPDATED => 'characterUpdated', // Nom de la méthode appelée
        ];
    }

    // Méthode appelée lorsque l'objet est créé
    public function characterCreated($event)
    {
        // Réception de l'objet Character avec le getter
        $character = $event->getCharacter();
        // Modification de l'objet
        $character->setIntelligence(250);

        if ('Dame' === $character->getKind()) {
            $character->setStrength($character->getStrength() + 5);
        } elseif ('Tourmenteuse' === $character->getKind()) {
            $character->setStrength($character->getStrength() - 5);
        }
    }

    public function characterUpdated($event)
    {
        // Réception de l'objet Character avec le getter
        $character = $event->getCharacter();
        // Modification de l'objet
        $character->setIntelligence(250);

        if ('Dame' === $character->getKind()) {
            $character->setStrength($character->getStrength() + 5);
        } elseif ('Tourmenteuse' === $character->getKind()) {
            $character->setStrength($character->getStrength() - 5);
        }
    }
}
