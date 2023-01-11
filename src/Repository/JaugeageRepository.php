<?php

namespace App\Repository;

use App\Entity\Cuve;
use App\Entity\Jaugeage;
use DateTime;
use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Jaugeage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Jaugeage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Jaugeage[]    findAll()
 * @method Jaugeage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JaugeageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Jaugeage::class);
    }

    /**
     * @param int $stationId
     * @return int|mixed|string|Jaugeage[]
     */
    public function getJaugeageByStation(int $stationId)
    {
        return $this->createQueryBuilder('j')
            ->leftJoin('j.cuve', 'cuve')
            ->leftJoin('cuve.station', 'station')
            ->where('station.id = :stationId')
            ->setParameter('stationId', $stationId)
            ->orderBy('j.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $cuveId
     * @return int|mixed|string
     * @throws Exception
     */
    public function getCuveDayJaugeage($cuveId)
    {
        $date = date('Y-m-d');
        $dateD = DateTime::createFromFormat('Y-m-d', $date);
        $dateF = DateTime::createFromFormat('Y-m-d', $date);
        $dateD->modify('- 1 days');

        $dateInf = $dateD->format('Y-m-d');
        $dateSup = $dateF->format('Y-m-d');

        $dateDeb = new DateTime($dateInf, new DateTimeZone('GMT'));
        $dateFin = new DateTime($dateSup, new DateTimeZone('GMT'));

        return $this->createQueryBuilder('j')
            ->leftJoin('j.cuve', 'cuve')
            ->where('cuve.id = :cuveId')
            ->andWhere('j.createdAt BETWEEN :dateDeb and :dateFin')
            ->setParameter('cuveId', $cuveId)
            ->setParameter('dateDeb', $dateDeb)
            ->setParameter('dateFin', $dateFin)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $cuveId
     * @param $jaugeageId
     * @return int|null
     */
    public function getLastCuveJaugeage($cuveId, $jaugeageId)
    {
        return $this->createQueryBuilder('j')
            ->leftJoin('j.cuve', 'cuve')
            ->where('cuve.id = :cuveId')
            ->andWhere('j.id < :jaugeageId')
            ->andWhere('j.isLast = 0')
            ->setParameter('cuveId', $cuveId)
            ->setParameter('jaugeageId', $jaugeageId)
            ->orderBy('j.id', 'DESC')
            ->getQuery()
            ->setMaxResults(1)
            ->getFirstResult();
    }

    // /**
    //  * @return Jaugeage[] Returns an array of Jaugeage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Jaugeage
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
