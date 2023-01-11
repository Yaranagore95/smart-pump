<?php

namespace App\Repository;

use App\Entity\StructureClient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StructureClient|null find($id, $lockMode = null, $lockVersion = null)
 * @method StructureClient|null findOneBy(array $criteria, array $orderBy = null)
 * @method StructureClient[]    findAll()
 * @method StructureClient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StructureClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StructureClient::class);
    }

    // /**
    //  * @return StructureClient[] Returns an array of StructureClient objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StructureClient
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
