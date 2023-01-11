<?php

namespace App\Repository;

use App\Entity\GlobalStockage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GlobalStockage|null find($id, $lockMode = null, $lockVersion = null)
 * @method GlobalStockage|null findOneBy(array $criteria, array $orderBy = null)
 * @method GlobalStockage[]    findAll()
 * @method GlobalStockage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GlobalStockageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GlobalStockage::class);
    }

    /**
     * @param $stationId
     * @return int|mixed|string|GlobalStockage[]
     */
    public function getGlobalStockagesByStationId($stationId)
    {
        return $this->createQueryBuilder('g')
            ->leftJoin('g.typeCarburant', 'typeCarburant')
            ->leftJoin('typeCarburant.station', 'station')
            ->where('station.id = :stationId')
            ->setParameter('stationId', $stationId)
            ->orderBy('g.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $typeCarburantId
     * @return int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function getTypeCarburantLastGlobalStockage($typeCarburantId)
    {
        return $this->createQueryBuilder('g')
            ->leftJoin('g.typeCarburant', 'typeCarburant')
            ->where('typeCarburant.id = :typeCarburantId')
            ->setParameter('typeCarburantId', $typeCarburantId)
            ->orderBy('g.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return GlobalStockage[] Returns an array of GlobalStockage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GlobalStockage
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
