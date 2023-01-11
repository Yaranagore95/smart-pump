<?php

namespace App\Repository;

use App\Entity\Pistolet;
use DateTime;
use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pistolet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pistolet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pistolet[]    findAll()
 * @method Pistolet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PistoletRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pistolet::class);
    }

    /**
     * @param $stationId
     * @return int|mixed|string
     */
    public function getPistoletByStation($stationId)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.pompe', 'pompe')
            ->leftJoin('pompe.station', 'station')
            ->where('station.id =:id')
            ->setParameter('id', $stationId)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }


    // /**
    //  * @return Pistolet[] Returns an array of Pistolet objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Pistolet
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
