<?php

namespace App\Repository;

use App\Entity\Cuve;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cuve|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cuve|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cuve[]    findAll()
 * @method Cuve[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CuveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cuve::class);
    }

    /**
     * @param int $stationId
     * @return int|mixed|string|Cuve[]
     */
    public function getCuveByStation(int $stationId)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.station', 'station')
            ->where('station.id = :stationId')
            ->setParameter('stationId', $stationId)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Cuve[] Returns an array of Cuve objects
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
    public function findOneBySomeField($value): ?Cuve
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
