<?php

namespace App\Repository;

use App\Entity\ClientStation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClientStation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClientStation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClientStation[]    findAll()
 * @method ClientStation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientStationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientStation::class);
    }

    // /**
    //  * @return ClientStation[] Returns an array of ClientStation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ClientStation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
