<?php

namespace App\Service;

use DateTime;
use App\Entity\Building;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BuildingRepository;

class BuildingService implements BuildingServiceInterface
{
    public function __construct(
        private BuildingRepository $buildingRepository,
        private EntityManagerInterface $em,
    ) {
    }

    public function create(): Building
    {
        $building = new Building();
        $building->setName('Château Lenora');
        $building->setSlug('chateau-lenora');
        $building->setCaste('Guerrier');
        $building->setStrength(1000);
        $building->setImage('/buildings/chateau-lenora.webp');
        $building->setPrice(200);
        $building->setStars(3);
        $building->setIdentifier(hash('sha1', uniqid()));
        $building->setCreation(new DateTime());
        $building->setModification(new DateTime());
        
        $this->em->persist($building);
        $this->em->flush();
        
        return $building;
    }

    public function findAll(): array
    {
        $buildingsFinal = [];
        $buildings = $this->buildingRepository->findAll();
        foreach ($buildings as $building) {
            $buildingsFinal[] = $building->toArray();
        }
        return $buildingsFinal;
    }

    public function update(Building $building): void
    {
        $building->setName('Château Silken');
        $building->setSlug('chateau-silken');
        $building->setCaste('Archer');
        $building->setStrength(1200);
        $building->setImage('/buildings/chateau-silken.webp');
        $building->setPrice(240);
        $building->setStars(4);
        $building->setModification(new DateTime());
        
        $this->em->persist($building);
        $this->em->flush();
    }

    public function delete(Building $building): void
    {
        $this->em->remove($building);
        $this->em->flush();
    }
}
