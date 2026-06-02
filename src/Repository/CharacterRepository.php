<?php

// src/Repository/CharacterRepository.php
// Dépôt Doctrine (Repository) contenant les requêtes personnalisées pour l'entité Character (notamment pour récupérer un personnage lié à son bâtiment de manière optimisée via son identifiant unique).

namespace App\Repository;

use App\Entity\Character;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Character>
 */
class CharacterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Character::class);
    }

    //    /**
    //     * @return Character[] Returns an array of Character objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Character
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findOneByIdentifier(string $identifier): ?Character
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'b')
            ->leftJoin('c.building', 'b')
            ->where('c.identifier = :identifier')
            ->setParameter('identifier', $identifier)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByLifeGreaterThanOrEqual(int $level)
    {
        return $this->createQueryBuilder('c')
            ->where('c.life >= :level')
            ->setParameter('level', $level)
            ->getQuery()
        ;
    }
}
