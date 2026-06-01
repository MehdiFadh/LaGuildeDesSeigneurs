<?php

// src/Listener/BuildingListener.php
// Écouteur d'événements (EventSubscriber) qui intercepte les modifications apportées aux bâtiments (par exemple, pour ajuster automatiquement leur force).

namespace App\Listener;

use App\Event\BuildingEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BuildingListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            BuildingEvent::BUILDING_UPDATED => 'buildingUpdated',
        ];
    }

    public function buildingUpdated($event)
    {
        $building = $event->getBuilding();
        $building->setStrength($building->getStrength() - 20);
    }
}
