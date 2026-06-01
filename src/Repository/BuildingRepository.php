<?php

// src/Repository/BuildingRepository.php
// Dépôt Doctrine (Repository) contenant les requêtes personnalisées pour l'entité Building (notamment pour récupérer un bâtiment avec ses personnages via son identifiant unique).

namespace App\Repository;

use App\Entity\Building;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Building>
 */
class BuildingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Building::class);
    }

    public function findOneByIdentifier(string $identifier): ?Building
    {
        return $this->createQueryBuilder('b')
            ->select('b', 'c')
            ->leftJoin('b.characters', 'c')
            ->where('b.identifier = :identifier')
            ->setParameter('identifier', $identifier)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByName(string $name): ?Building
    {
        return $this->createQueryBuilder('b')
            ->select('b', 'c')
            ->leftJoin('b.characters', 'c')
            ->where('b.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getNameBuildingOrderDESC(string $attribut): array
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.'.$attribut, 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}
