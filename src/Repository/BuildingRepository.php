<?php

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
}
