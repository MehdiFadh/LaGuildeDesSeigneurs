<?php

// src/Event/CharacterEvent.php
// Événement Symfony personnalisé déclenché lors des actions sur les personnages (création avant/après persistance, et mise à jour) pour notifier les écouteurs.

namespace App\Event;

use App\Entity\Character;
use Symfony\Contracts\EventDispatcher\Event;

class CharacterEvent extends Event
{
    public const CHARACTER_CREATED_POST_DATABASE = 'app.character.created.post.database';
    // Constante pour le nom de l'event, nommage par convention
    public const CHARACTER_CREATED = 'app.character.created';
    public const CHARACTER_UPDATED = 'app.character.updated';

    // Injection de l'objet
    public function __construct(
        protected Character $character,
    ) {
    }

    // Getter pour l'objet
    public function getCharacter(): Character
    {
        return $this->character;
    }
}
