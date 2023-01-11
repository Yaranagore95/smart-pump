<?php

namespace App\Repository;

use App\Entity\Indexation;
use DateTime;
use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Indexation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Indexation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Indexation[]    findAll()
 * @method Indexation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IndexationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Indexation::class);
    }

    /**
     * @param $stationId
     * @return int|mixed|string|Indexation[]
     */
    public function getStationIndexations($stationId)
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.pistolet', 'pistolet')
            ->leftJoin('pistolet.pompe', 'pompe')
            ->leftJoin('pompe.station', 'station')
            ->where('station.id = :stationId')
            ->orderBy('i.createdAt', 'DESC')
            ->setParameter('stationId', $stationId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $stationId
     * @param DateTime $dateInf
     * @param DateTime $dateSup
     * @return int|mixed|string
     */
    public function getStationIndexationsByDate($stationId, DateTime $dateInf, DateTime $dateSup)
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.pistolet', 'pistolet')
            ->leftJoin('pistolet.pompe', 'pompe')
            ->leftJoin('pompe.station', 'station')
            ->where('station.id = :stationId')
            ->andWhere('i.createdAt between :dateInf and :dateSup')
            ->orderBy('i.createdAt', 'DESC')
            ->setParameter('stationId', $stationId)
            ->setParameter('dateInf', $dateInf)
            ->setParameter('dateSup', $dateSup)
            ->getQuery()
            ->getResult();
    }


    /**
     * @param $pistoletId
     * @return int|mixed|string
     * @throws Exception
     */
    public function getPistoletDayIndexation($pistoletId)
    {
        $date = date('Y-m-d');
        $dateD = DateTime::createFromFormat('Y-m-d', $date);
        $dateF = DateTime::createFromFormat('Y-m-d', $date);
        $dateD->modify('- 1 days');

        $dateInf = $dateD->format('Y-m-d');
        $dateSup = $dateF->format('Y-m-d');

        $dateDeb = new DateTime($dateInf, new DateTimeZone('GMT'));
        $dateFin = new DateTime($dateSup, new DateTimeZone('GMT'));

        return $this->createQueryBuilder('i')
            ->leftJoin('i.pistolet', 'pistolet')
            ->where('pistolet.id = :id')
            ->andWhere('i.createdAt BETWEEN :dateDeb and :dateFin')
            ->setParameter('id', $pistoletId)
            ->setParameter('dateDeb', $dateDeb)
            ->setParameter('dateFin', $dateFin)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Indexation[] Returns an array of Indexation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Indexation
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
